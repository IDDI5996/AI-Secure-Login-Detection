import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content,
        }
    }
});

// Subscribe to suspicious login channel
if (window.Echo) {
    window.Echo.private('admin.suspicious-logins')
        .listen('SuspiciousLoginDetected', (e) => {
            // Show notification
            showNotification({
                title: '🚨 Suspicious Login Detected',
                message: `${e.user} from ${e.location}`,
                type: 'danger',
                data: e
            });
            
            // Update dashboard stats in real-time
            updateDashboardStats();
        });
}