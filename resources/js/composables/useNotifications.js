import { ref, reactive } from 'vue';

/**
 * Notification composable for managing in-app notifications
 */
export function useNotifications() {
    const notifications = ref([]);
    
    const defaultOptions = {
        type: 'info',
        duration: 5000,
        persistent: false,
        actions: []
    };

    /**
     * Add a new notification
     * @param {string} message 
     * @param {object} options 
     */
    function addNotification(message, options = {}) {
        const notification = {
            id: Date.now() + Math.random(),
            message,
            ...defaultOptions,
            ...options,
            timestamp: new Date()
        };

        notifications.value.push(notification);

        // Auto-remove non-persistent notifications
        if (!notification.persistent && notification.duration > 0) {
            setTimeout(() => {
                removeNotification(notification.id);
            }, notification.duration);
        }

        return notification.id;
    }

    /**
     * Remove a notification by ID
     * @param {string|number} id 
     */
    function removeNotification(id) {
        const index = notifications.value.findIndex(n => n.id === id);
        if (index > -1) {
            notifications.value.splice(index, 1);
        }
    }

    /**
     * Clear all notifications
     */
    function clearNotifications() {
        notifications.value = [];
    }

    /**
     * Show success notification
     * @param {string} message 
     * @param {object} options 
     */
    function success(message, options = {}) {
        return addNotification(message, { ...options, type: 'success' });
    }

    /**
     * Show error notification
     * @param {string} message 
     * @param {object} options 
     */
    function error(message, options = {}) {
        return addNotification(message, { ...options, type: 'error', duration: 8000 });
    }

    /**
     * Show warning notification
     * @param {string} message 
     * @param {object} options 
     */
    function warning(message, options = {}) {
        return addNotification(message, { ...options, type: 'warning' });
    }

    /**
     * Show info notification
     * @param {string} message 
     * @param {object} options 
     */
    function info(message, options = {}) {
        return addNotification(message, { ...options, type: 'info' });
    }

    return {
        notifications,
        addNotification,
        removeNotification,
        clearNotifications,
        success,
        error,
        warning,
        info
    };
}