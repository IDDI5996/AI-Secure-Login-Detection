#!/usr/bin/env python3
"""
AI Model Predictor - Called by Laravel via PHP exec()
Usage: python predict.py <feature_json>
Returns: JSON with risk_score, is_suspicious, factors
"""

import sys
import json
import joblib
import pandas as pd
import numpy as np
from pathlib import Path

MODEL_DIR = Path(__file__).parent

class LoginAnomalyDetector:
    def __init__(self):
        self.model = joblib.load(MODEL_DIR / 'isolation_forest.joblib')
        self.metadata = json.load(open(MODEL_DIR / 'metadata.json'))
        self.feature_columns = self.metadata['feature_columns']
        self.score_stats = self.metadata['score_stats']
        
    def predict(self, features):
        # Create DataFrame with correct column order
        df = pd.DataFrame([features], columns=self.feature_columns)
        
        # Get anomaly score (lower = more anomalous)
        decision_score = self.model.decision_function(df)[0]
        
        # Normalize to risk score (0-100)
        min_score = self.score_stats['decision_min']
        max_score = self.score_stats['decision_max']
        
        if max_score - min_score > 0:
            risk_score = (1 - (decision_score - min_score) / (max_score - min_score)) * 100
        else:
            risk_score = 50
        
        risk_score = max(0, min(100, risk_score))
        is_suspicious = risk_score >= 80
        
        factors = self._get_risk_factors(df)
        
        # Convert numpy types to native Python types for JSON serialization
        result = {
            'risk_score': float(round(risk_score, 2)),
            'is_suspicious': bool(is_suspicious),    # ensures Python bool
            'factors': factors,
            'decision_score': float(round(decision_score, 4))
        }
        return result
    
    def _get_risk_factors(self, df):
        factors = []
        for col in self.feature_columns:
            value = df[col].iloc[0]
            
            # Convert numpy values to native Python types
            if col == 'hour' and (value < 6 or value > 22):
                factors.append({'factor': 'unusual_hour', 'value': int(value), 'risk': 0.6})
            elif col == 'is_weekend' and value == 1:
                factors.append({'factor': 'weekend_login', 'value': int(value), 'risk': 0.4})
            elif col == 'failed_count_24h' and value > 3:
                factors.append({'factor': 'multiple_failures', 'value': int(value), 'risk': min(0.8, value * 0.1)})
            elif col == 'fail_rate_24h' and value > 0.5:
                factors.append({'factor': 'high_fail_rate', 'value': float(value), 'risk': min(0.9, value * 0.5)})
            elif col == 'unique_ips_24h' and value > 5:
                factors.append({'factor': 'multiple_ips', 'value': int(value), 'risk': min(0.7, value * 0.05)})
            elif col == 'unique_countries_24h' and value > 1:
                factors.append({'factor': 'multiple_countries', 'value': int(value), 'risk': min(0.8, value * 0.3)})
            elif col == 'unique_devices_24h' and value > 3:
                factors.append({'factor': 'multiple_devices', 'value': int(value), 'risk': min(0.6, value * 0.1)})
        return factors

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({'error': 'Missing features argument'}))
        sys.exit(1)
    
    try:
        features = json.loads(sys.argv[1])
        detector = LoginAnomalyDetector()
        result = detector.predict(features)
        print(json.dumps(result))
    except Exception as e:
        print(json.dumps({'error': str(e)}))
        sys.exit(1)