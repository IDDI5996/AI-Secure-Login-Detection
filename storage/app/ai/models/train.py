#!/usr/bin/env python3
"""
Standalone training script for Isolation Forest anomaly detector.
Compatible with both:
- Raw login data (with columns like 'Status', 'IP Address', etc.)
- Pre‑extracted feature CSV (15 features only)
"""

import argparse
import json
import pandas as pd
import numpy as np
from sklearn.ensemble import IsolationForest
import joblib
from pathlib import Path
from datetime import datetime

def parse_args():
    parser = argparse.ArgumentParser(description="Train suspicious-login detector from CSV.")
    parser.add_argument("csv_path", help="Path to the CSV file")
    parser.add_argument("--output", default="detector_package_retrained",
                        help="Output directory for model and metadata")
    parser.add_argument("--n-estimators", type=int, default=8,
                        help="Number of trees in Isolation Forest")
    parser.add_argument("--contamination", default="auto",
                        help="Contamination parameter for Isolation Forest")
    parser.add_argument("--random-state", type=int, default=42,
                        help="Random seed for reproducibility")
    return parser.parse_args()

def prepare_features(df):
    """
    Convert raw login data into the 15 features used by the model.
    """
    # Ensure datetime column is parsed
    if 'Attempted At' in df.columns:
        time_col = 'Attempted At'
        df[time_col] = pd.to_datetime(df[time_col])
    elif 'attempted_at' in df.columns:
        time_col = 'attempted_at'
        df[time_col] = pd.to_datetime(df[time_col])
    else:
        # Fallback: create a dummy time column
        time_col = '_time'
        df['_time'] = pd.to_datetime('now') - pd.to_timedelta(np.arange(len(df)), unit='s')
    
    # Status
    df['status'] = df['Status'].apply(lambda x: 1 if x == 'Successful' else 0)
    
    # Time features
    df['hour'] = df[time_col].dt.hour
    df['day_of_week'] = df[time_col].dt.dayofweek + 1
    df['minute'] = df[time_col].dt.minute
    df['is_weekend'] = df[time_col].dt.dayofweek.isin([5,6]).astype(int)
    
    # Velocity features (per user)
    user_col = 'User Name' if 'User Name' in df.columns else 'Username'
    df['login_count_24h'] = 0
    df['failed_count_24h'] = 0
    df['fail_rate_24h'] = 0.0
    df['unique_ips_24h'] = 0
    df['unique_countries_24h'] = 0
    df['unique_devices_24h'] = 0
    
    for user in df[user_col].unique():
        mask = df[user_col] == user
        user_attempts = df.loc[mask].copy().sort_values(time_col)
        for idx, row in user_attempts.iterrows():
            end_time = row[time_col]
            start_time = end_time - pd.Timedelta(hours=24)
            window = user_attempts[(user_attempts[time_col] >= start_time) & (user_attempts[time_col] <= end_time)]
            df.loc[idx, 'login_count_24h'] = len(window)
            df.loc[idx, 'failed_count_24h'] = len(window[window['Status'] == 'Failed']) if 'Status' in window else 0
            df.loc[idx, 'fail_rate_24h'] = df.loc[idx, 'failed_count_24h'] / max(1, df.loc[idx, 'login_count_24h'])
            df.loc[idx, 'unique_ips_24h'] = window['IP Address'].nunique() if 'IP Address' in window else 0
            df.loc[idx, 'unique_countries_24h'] = window['Country'].nunique() if 'Country' in window else 0
            df.loc[idx, 'unique_devices_24h'] = window['Device Type'].nunique() if 'Device Type' in window else 0
    
    # Global frequency features
    ip_col = 'IP Address'
    country_col = 'Country'
    device_col = 'Device Type'
    browser_col = 'Browser'
    
    ip_freq = df[ip_col].value_counts()
    country_freq = df[country_col].value_counts()
    device_freq = df[device_col].value_counts()
    browser_freq = df[browser_col].value_counts()
    
    df['ip_freq'] = df[ip_col].map(ip_freq).fillna(0).astype(int)
    df['country_freq'] = df[country_col].map(country_freq).fillna(0).astype(int)
    df['device_type_freq'] = df[device_col].map(device_freq).fillna(0).astype(int)
    df['browser_freq'] = df[browser_col].map(browser_freq).fillna(0).astype(int)
    
    feature_columns = [
        'status', 'hour', 'day_of_week', 'minute', 'is_weekend',
        'login_count_24h', 'failed_count_24h', 'fail_rate_24h',
        'unique_ips_24h', 'unique_countries_24h', 'unique_devices_24h',
        'ip_freq', 'country_freq', 'device_type_freq', 'browser_freq'
    ]
    return df[feature_columns], feature_columns

def main():
    args = parse_args()
    df = pd.read_csv(args.csv_path)
    print(f"Loaded {len(df)} rows from {args.csv_path}")
    
    # Detect if CSV is raw data or already feature-extracted
    if 'Status' in df.columns and 'IP Address' in df.columns:
        print("Detected raw login data. Extracting features...")
        X, feature_columns = prepare_features(df)
        print(f"Extracted {len(feature_columns)} features")
    else:
        # Assume CSV already has the 15 features
        feature_columns = df.columns.tolist()
        X = df[feature_columns].values
        print(f"Using {len(feature_columns)} pre‑extracted features")
    
    # Train Isolation Forest
    model = IsolationForest(
        n_estimators=args.n_estimators,
        contamination=args.contamination,
        random_state=args.random_state,
        n_jobs=-1
    )
    model.fit(X)
    
    # Decision scores stats
    decision_scores = model.decision_function(X)
    score_stats = {
        'threshold': 0.0,
        'decision_min': float(np.min(decision_scores)),
        'decision_p05': float(np.percentile(decision_scores, 5)),
        'decision_p50': float(np.percentile(decision_scores, 50)),
        'decision_p95': float(np.percentile(decision_scores, 95)),
        'decision_max': float(np.max(decision_scores)),
    }
    
    # Metadata
    metadata = {
        'source_csv': args.csv_path,
        'rows': len(df),
        'feature_columns': feature_columns,
        'column_mapping': {
            'user_id': 'Username',
            'timestamp': 'Attempted At',
            'ip': 'IP Address',
            'status': 'Status',
            'username': 'User Name',
            'email': 'Email',
            'city': 'City',
            'country': 'Country',
            'device_type': 'Device Type',
            'browser': 'Browser',
            'verification_status': 'Verification Status'
        },
        'score_stats': score_stats,
        'n_estimators': args.n_estimators,
        'contamination': args.contamination,
        'trained_at': datetime.now().isoformat()
    }
    
    output_dir = Path(args.output)
    output_dir.mkdir(parents=True, exist_ok=True)
    
    model_path = output_dir / 'isolation_forest.joblib'
    joblib.dump(model, model_path, protocol=4)
    
    metadata_path = output_dir / 'metadata.json'
    with open(metadata_path, 'w', encoding='utf-8') as f:
        json.dump(metadata, f, indent=2, default=str)
    
    print(f"Model saved to {model_path}")
    print(f"Metadata saved to {metadata_path}")
    print(f"Score stats: {score_stats}")
    
    # Save a feature sample
    sample_features = X[0] if len(X) > 0 else {}
    if isinstance(sample_features, np.ndarray):
        sample_features = {feature_columns[i]: float(sample_features[i]) for i in range(len(feature_columns))}
    with open(output_dir / 'feature_sample.json', 'w') as f:
        json.dump(sample_features, f, indent=2)
    
    print("Training complete.")

if __name__ == "__main__":
    main()