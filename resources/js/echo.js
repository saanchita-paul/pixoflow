import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;
import toastr from 'toastr';
import 'toastr/build/toastr.min.css';


window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
        authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                axios.post('/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                })
                    .then(response => {
                        callback(false, response.data);
                    })
                    .catch(error => {
                        callback(true, error);
                    });
            }
        };
    },
});

// window.Echo.private('notification.'+1)
//     .notification( (event) => {
//         alert('notification coming');
//         console.log(event);
//     });

// window.Echo.private('notification.admin')
//     .notification((event) => {
//         // console.log('A file was claimed by:', e.claimed_by, 'File ID:', e.file_id);
//         // alert(`File "${event.file_name}" was claimed by ${event.claimed_by}`);
//         // alert('File claimed!');
//         toastr.info(`File "${event.file_name}" was claimed by ${event.claimed_by}`, 'File Claimed');
//         console.log(event);
//     });

window.Echo.private('notification.admin')
    .notification((event) => {
        console.log('[Notification Received]', event);
        if (event.status) {
            // Status update
            toastr.success(`File "${event.file_name}" status updated to "${event.status}" by ${event.claimed_by}`, 'Status Updated');
        } else {
            // File claimed
            toastr.info(`File "${event.file_name}" was claimed by ${event.claimed_by}`, 'File Claimed');
        }
        console.log(event);
    });
