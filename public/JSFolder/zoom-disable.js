/**
 * Zoom Disable JavaScript
 * 
 * This script prevents keyboard zoom shortcuts (Ctrl +/-, Ctrl + scroll wheel)
 * to maintain consistent UI layout and prevent accidental zooming.
 */

document.addEventListener('DOMContentLoaded', function() {
    disableZoom();
});

/**
 * Disable zoom functionality
 */
function disableZoom() {
    // Prevent Ctrl + +/- zoom
    document.addEventListener('keydown', function(e) {
        // Check for Ctrl or Cmd key combinations
        if (e.ctrlKey || e.metaKey) {
            // Prevent zoom with + or - keys
            if (e.key === '+' || e.key === '=' || e.key === '-' || e.key === '_') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            
            // Prevent zoom with 0 key (reset zoom)
            if (e.key === '0') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }
    });
    
    // Prevent Ctrl + scroll wheel zoom
    document.addEventListener('wheel', function(e) {
        if (e.ctrlKey || e.metaKey) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, { passive: false });
    
    // Prevent pinch-to-zoom on touch devices
    let touchStartDistance = 0;
    
    document.addEventListener('touchstart', function(e) {
        if (e.touches.length === 2) {
            touchStartDistance = Math.hypot(
                e.touches[0].pageX - e.touches[1].pageX,
                e.touches[0].pageY - e.touches[1].pageY
            );
        }
    });
    
    document.addEventListener('touchmove', function(e) {
        if (e.touches.length === 2 && touchStartDistance > 0) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, { passive: false });
    
    // Prevent browser zoom gestures
    document.addEventListener('gesturestart', function(e) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    });
    
    // Prevent double-tap zoom on mobile
    let lastTouchEnd = 0;
    
    document.addEventListener('touchend', function(e) {
        const currentTime = new Date().getTime();
        const tapLength = currentTime - lastTouchEnd;
        
        if (tapLength < 300 && tapLength > 0) {
            e.preventDefault();
        }
        
        lastTouchEnd = currentTime;
    });
    
    // Disable browser's built-in zoom controls
    disableBrowserZoomControls();
}

/**
 * Disable browser's built-in zoom controls
 */
function disableBrowserZoomControls() {
    // Disable browser menu zoom options
    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        return false;
    });
    
    // Prevent browser zoom via menu
    document.addEventListener('mousedown', function(e) {
        // Prevent right-click zoom
        if (e.button === 2) {
            e.preventDefault();
            return false;
        }
    });
    
    // Disable accessibility zoom if needed
    const style = document.createElement('style');
    style.innerHTML = `
        /* Disable browser zoom */
        body {
            touch-action: manipulation;
            -ms-touch-action: manipulation;
            -webkit-touch-action: manipulation;
            -moz-touch-action: manipulation;
        }
        
        /* Disable pinch zoom */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            body {
                -webkit-text-size-adjust: none;
                -moz-text-size-adjust: none;
                -ms-text-size-adjust: none;
                text-size-adjust: none;
            }
        }
        
        /* Hide browser zoom controls */
        .zoom-controls {
            display: none !important;
        }
    `;
    document.head.appendChild(style);
}

/**
 * Re-enable zoom functionality (if needed)
 */
function enableZoom() {
    // Remove event listeners
    document.removeEventListener('keydown', preventZoom);
    document.removeEventListener('wheel', preventZoom);
    document.removeEventListener('touchstart', preventTouchZoom);
    document.removeEventListener('touchmove', preventTouchZoom);
    document.removeEventListener('gesturestart', preventGestureZoom);
    document.removeEventListener('touchend', preventDoubleTapZoom);
    document.removeEventListener('contextmenu', preventContextMenu);
    document.removeEventListener('mousedown', preventRightClick);
    
    // Remove the style tag
    const zoomStyle = document.querySelector('style[data-zoom-disable]');
    if (zoomStyle) {
        zoomStyle.remove();
    }
}

/**
 * Helper functions for event prevention
 */
function preventZoom(e) {
    if ((e.ctrlKey || e.metaKey) && (e.key === '+' || e.key === '-' || e.key === '0' || e.key === '=')) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
}

function preventWheelZoom(e) {
    if (e.ctrlKey || e.metaKey) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
}

function preventTouchZoom(e) {
    if (e.touches.length === 2) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
}

function preventGestureZoom(e) {
    e.preventDefault();
    e.stopPropagation();
    return false;
}

function preventDoubleTapZoom(e) {
    const currentTime = new Date().getTime();
    const tapLength = currentTime - lastTouchEnd;
    
    if (tapLength < 300 && tapLength > 0) {
        e.preventDefault();
    }
    
    lastTouchEnd = currentTime;
}

function preventContextMenu(e) {
    e.preventDefault();
    return false;
}

function preventRightClick(e) {
    if (e.button === 2) {
        e.preventDefault();
        return false;
    }
}

// Export functions for global access if needed
window.zoomControl = {
    disable: disableZoom,
    enable: enableZoom
};

// Add console log for debugging
console.log('Zoom control initialized - Keyboard zoom disabled');
