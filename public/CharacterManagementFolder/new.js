let currentStep = 1;
const totalSteps = 4;
let selectedAlignment = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize file upload handlers
    initializeFileUpload();
    
    // Initialize form submission handler
    initializeFormSubmission();
    
    // Initialize color input handler
    initializeColorInput();
});

function moveStep(delta) {
    const nextStep = currentStep + delta;
    if (nextStep < 1 || nextStep > totalSteps) return;

    const currentStepEl = document.querySelector(`[data-step="${currentStep}"]`);
    const nextStepEl = document.querySelector(`[data-step="${nextStep}"]`);

    if (!currentStepEl || !nextStepEl) return;

    currentStepEl.classList.remove('active');
    nextStepEl.classList.add('active');

    updateStepIndicators(currentStep, nextStep);
    currentStep = nextStep;
    
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    if (currentStep > 1) {
        if (prevBtn) {
            prevBtn.classList.remove('opacity-0', 'pointer-events-none');
        }
    } else {
        if (prevBtn) {
            prevBtn.classList.add('opacity-0', 'pointer-events-none');
        }
    }

    if (currentStep === totalSteps) {
        if (nextBtn) nextBtn.classList.add('hidden');
        if (submitBtn) submitBtn.classList.remove('hidden');
    } else {
        if (nextBtn) nextBtn.classList.remove('hidden');
        if (submitBtn) submitBtn.classList.add('hidden');
    }
}

function updateStepIndicators(oldStep, newStep) {
    const oldPill = document.getElementById(`pill-${oldStep}`);
    const newPill = document.getElementById(`pill-${newStep}`);

    if (!oldPill || !newPill) return;

    if (newStep > oldStep) {
        oldPill.classList.remove('active');
        oldPill.classList.add('completed');
        const oldSpan = oldPill.querySelector('span');
        if (oldSpan) {
            oldSpan.classList.replace('text-indigo-600', 'text-emerald-600');
        }
    } else {
        newPill.classList.remove('completed');
        const newSpan = newPill.querySelector('span');
        if (newSpan) {
            newSpan.classList.replace('text-emerald-600', 'text-indigo-600');
        }
    }
    
    newPill.classList.add('active');
}

function syncPreview(type, val) {
    const elements = {
        name: 'previewName',
        creator: 'previewCreator',
        description: 'previewDesc',
        color: 'previewColorBox'
    };

    const target = document.getElementById(elements[type]);
    if (!target) return;

    if (!val || val.trim() === '') {
        const defaults = {
            name: 'Untitled Character',
            creator: 'Unknown Creator',
            description: 'Start entering character details to see them update here in real-time...',
            color: '#6366f1'
        };
        
        if (type === 'color') {
            target.style.backgroundColor = defaults[type];
        } else {
            target.innerText = defaults[type];
        }
    } else {
        if (type === 'color') {
            target.style.backgroundColor = val;
            // Also update the alignment badge color if one is selected
            const previewAlignment = document.getElementById('previewAlignment');
            if (previewAlignment && selectedAlignment) {
                updateAlignmentBadgeColor(previewAlignment, val);
            }
        } else {
            target.innerText = val;
        }
    }
}

function selectAlignment(alignment, element) {
    selectedAlignment = alignment;
    
    // Remove selected class from all alignment cards
    document.querySelectorAll('.alignment-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selected class to clicked card
    if (element) {
        element.classList.add('selected');
    }
    
    // Update hidden select input
    const alignmentInput = document.querySelector('select[name*="alignment"]');
    if (alignmentInput) {
        alignmentInput.value = alignment;
    }
    
    // Update preview badge
    const previewAlignment = document.getElementById('previewAlignment');
    if (previewAlignment) {
        previewAlignment.textContent = alignment;
        
        // Get current color
        const colorInput = document.querySelector('input[type="color"]');
        const currentColor = colorInput ? colorInput.value : '#6366f1';
        updateAlignmentBadgeColor(previewAlignment, currentColor);
    }
}

function updateAlignmentBadgeColor(badge, color) {
    // Set the badge background to use the character color
    badge.style.backgroundColor = color;
    badge.style.borderColor = color;
}

function initializeFileUpload() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.querySelector('input[type="file"]');

    if (dropZone && fileInput) {
        // Click handler for drop zone
        dropZone.addEventListener('click', function(e) {
            if (!e.target.closest('button')) {
                fileInput.click();
            }
        });

        // Drag and drop handlers
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', function() {
            dropZone.classList.remove('drag-over');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                handleFile(fileInput);
            }
        });

        // File input change handler
        fileInput.addEventListener('change', function() {
            handleFile(this);
        });
    }
}

function handleFile(input) {
    const file = input.files[0];
    if (!file) return;

    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        alert('Please upload a valid image file (JPEG, PNG, GIF, or WebP)');
        input.value = '';
        return;
    }

    // Validate file size (max 10MB)
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file.size > maxSize) {
        alert('File size must be less than 10MB');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
        const imgResult = document.getElementById('imgResult');
        const previewMainImg = document.getElementById('previewMainImg');
        const dropZoneUI = document.getElementById('dropZoneUI');
        const previewContainer = document.getElementById('previewContainer');

        if (imgResult) imgResult.src = e.target.result;
        if (previewMainImg) previewMainImg.src = e.target.result;
        if (dropZoneUI) dropZoneUI.classList.add('hidden');
        if (previewContainer) previewContainer.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function resetFile(event) {
    if (event) {
        event.stopPropagation();
    }
    
    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput) {
        fileInput.value = '';
    }

    const imgResult = document.getElementById('imgResult');
    const previewMainImg = document.getElementById('previewMainImg');
    const dropZoneUI = document.getElementById('dropZoneUI');
    const previewContainer = document.getElementById('previewContainer');

    if (imgResult) imgResult.src = '';
    if (previewMainImg) {
        // Reset to placeholder
        previewMainImg.src = 'https://placehold.co/400x500/f1f5f9/94a3b8?text=Character+Image';
    }
    if (dropZoneUI) dropZoneUI.classList.remove('hidden');
    if (previewContainer) previewContainer.classList.add('hidden');
}

function initializeFormSubmission() {
    const characterForm = document.getElementById('characterForm');
    const submitBtn = document.getElementById('submitBtn');

    if (characterForm && submitBtn) {
        characterForm.addEventListener('submit', function(e) {
            // Show loading state
            const btnText = document.getElementById('submitBtnText');
            const btnLoading = document.getElementById('submitBtnLoading');
            
            if (btnText && btnLoading) {
                btnText.classList.add('hidden');
                btnLoading.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            }
        });
    }
}

function initializeColorInput() {
    const colorPicker = document.getElementById('colorPicker');
    const colorHexInput = document.querySelector('input[name*="colorCode"]');
    
    // Initialize color picker with hex input value if exists
    if (colorPicker && colorHexInput && colorHexInput.value) {
        colorPicker.value = colorHexInput.value;
    }
    
    // Sync preview when either input changes
    if (colorPicker) {
        colorPicker.addEventListener('input', function() {
            syncPreview('color', this.value);
        });
    }
    
    if (colorHexInput) {
        colorHexInput.addEventListener('input', function() {
            syncPreview('color', this.value);
        });
    }
}

// Update color hex input when color picker changes
function updateColorHex(colorValue) {
    const colorHexInput = document.querySelector('input[name*="colorCode"]');
    if (colorHexInput) {
        colorHexInput.value = colorValue.toUpperCase();
    }
    syncPreview('color', colorValue);
}

// Update color picker when hex input changes
function syncColorFromHex(hexValue) {
    const colorPicker = document.getElementById('colorPicker');
    
    // Validate hex format
    if (/^#[0-9A-F]{6}$/i.test(hexValue)) {
        if (colorPicker) {
            colorPicker.value = hexValue;
        }
        syncPreview('color', hexValue);
    } else if (hexValue.length === 7) {
        // Invalid hex format - show visual feedback
        const colorHexInput = document.querySelector('input[name*="colorCode"]');
        if (colorHexInput) {
            colorHexInput.style.borderColor = '#ef4444';
            setTimeout(() => {
                colorHexInput.style.borderColor = '';
            }, 1000);
        }
    }
}
