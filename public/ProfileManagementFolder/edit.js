let currentStep = 1;
const totalSteps = 4;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize password strength checker
    const passwordInput = document.getElementById('passwordInput');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const strengthContainer = document.getElementById('passwordStrengthContainer');
            if (this.value.length > 0) {
                if (strengthContainer) {
                    strengthContainer.style.display = 'block';
                }
                checkPasswordStrength();
            } else {
                if (strengthContainer) {
                    strengthContainer.style.display = 'none';
                }
            }
        });
    }
    
    // Initialize form submission handler
    const profileForm = document.getElementById('profileForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (profileForm && submitBtn) {
        profileForm.addEventListener('submit', function() {
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
    
    // Initialize drop zone
    const dropZone = document.getElementById('dropZone');
    if (dropZone) {
        dropZone.addEventListener('click', function() {
            const fileInput = this.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.click();
            }
        });
        
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-indigo-500', 'bg-indigo-50');
        });
        
        dropZone.addEventListener('dragleave', function() {
            this.classList.remove('border-indigo-500', 'bg-indigo-50');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-500', 'bg-indigo-50');
            
            const fileInput = this.querySelector('input[type="file"]');
            if (fileInput && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFile(fileInput);
            }
        });
    }
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
        displayName: 'previewDisplayName',
        email: 'previewEmail',
        bio: 'previewBio'
    };

    const target = document.getElementById(elements[type]);
    if (!target) return;

    if (!val || val.trim() === '') {
        if (type === 'bio') {
            target.innerText = 'No bio added yet.';
        }
        return;
    } else {
        target.innerText = val;
    }
    
    // Update initial in avatar if display name changes
    if (type === 'displayName') {
        const previewInitial = document.getElementById('previewInitial');
        if (previewInitial && val) {
            previewInitial.textContent = val.charAt(0).toUpperCase();
        }
    }
}

function selectZodiac(element, sign) {
    if (!element || !sign) return;
    
    // Remove selected class from all zodiac cards
    document.querySelectorAll('.zodiac-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selected class to clicked card
    element.classList.add('selected');
    
    // Update hidden select field
    const zodiacSelect = document.querySelector('select[name*="zodiacSign"]');
    if (zodiacSelect) {
        zodiacSelect.value = sign;
    }
    
    // Update preview
    const zodiacSymbols = {
        'Aries': '♈', 'Taurus': '♉', 'Gemini': '♊', 'Cancer': '♋',
        'Leo': '♌', 'Virgo': '♍', 'Libra': '♎', 'Scorpio': '♏',
        'Sagittarius': '♐', 'Capricorn': '♑', 'Aquarius': '♒', 'Pisces': '♓'
    };
    
    const previewSymbol = document.getElementById('previewZodiacSymbol');
    const previewName = document.getElementById('previewZodiacName');
    
    if (previewSymbol) {
        previewSymbol.textContent = zodiacSymbols[sign] || '—';
    }
    if (previewName) {
        previewName.textContent = sign;
    }
}

function handleFile(input) {
    if (!input || !input.files || input.files.length === 0) return;
    
    const file = input.files[0];
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const imgResult = document.getElementById('imgResult');
        const dropZoneUI = document.getElementById('dropZoneUI');
        const previewContainer = document.getElementById('previewContainer');
        const previewAvatar = document.getElementById('previewAvatar');
        const previewAvatarContainer = document.getElementById('previewAvatarContainer');
        const previewInitial = document.getElementById('previewInitial');
        
        if (imgResult) {
            imgResult.src = e.target.result;
        }
        
        if (dropZoneUI) {
            dropZoneUI.classList.add('hidden');
        }
        
        if (previewContainer) {
            previewContainer.classList.remove('hidden');
        }
        
        // Update preview avatar
        if (previewAvatarContainer) {
            if (!previewAvatar) {
                // Create img element if it doesn't exist
                const img = document.createElement('img');
                img.id = 'previewAvatar';
                img.className = 'w-full h-full object-cover';
                img.src = e.target.result;
                
                // Hide initial if it exists
                if (previewInitial) {
                    previewInitial.style.display = 'none';
                }
                
                previewAvatarContainer.innerHTML = '';
                previewAvatarContainer.appendChild(img);
            } else {
                previewAvatar.src = e.target.result;
            }
        }
    };
    
    reader.readAsDataURL(file);
}

function resetFile(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const fileInput = document.querySelector('input[type="file"]');
    const imgResult = document.getElementById('imgResult');
    const dropZoneUI = document.getElementById('dropZoneUI');
    const previewContainer = document.getElementById('previewContainer');
    const previewAvatarContainer = document.getElementById('previewAvatarContainer');
    const previewInitial = document.getElementById('previewInitial');
    
    if (fileInput) {
        fileInput.value = '';
    }
    
    if (imgResult) {
        imgResult.src = '';
    }
    
    if (dropZoneUI) {
        dropZoneUI.classList.remove('hidden');
    }
    
    if (previewContainer) {
        previewContainer.classList.add('hidden');
    }
    
    // Reset preview avatar to initial
    if (previewAvatarContainer && previewInitial) {
        const displayNameInput = document.querySelector('input[name*="displayName"]');
        const initial = displayNameInput && displayNameInput.value ? displayNameInput.value.charAt(0).toUpperCase() : 'U';
        
        previewAvatarContainer.innerHTML = `
            <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-4xl sm:text-5xl font-bold">
                <span id="previewInitial">${initial}</span>
            </div>
        `;
    }
}

function togglePassword() {
    const passwordInput = document.getElementById('passwordInput');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (!passwordInput || !toggleIcon) return;
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

function checkPasswordStrength() {
    const passwordInput = document.getElementById('passwordInput');
    if (!passwordInput) return;
    
    const password = passwordInput.value;
    const strengthBars = document.querySelectorAll('.strength-bar');
    const strengthText = document.getElementById('strengthText');
    
    // Check requirements
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password)
    };
    
    // Update requirement indicators
    updateRequirement('req-length', requirements.length);
    updateRequirement('req-uppercase', requirements.uppercase);
    updateRequirement('req-lowercase', requirements.lowercase);
    updateRequirement('req-number', requirements.number);
    
    // Calculate strength
    let strength = 0;
    if (requirements.length) strength++;
    if (requirements.uppercase) strength++;
    if (requirements.lowercase) strength++;
    if (requirements.number) strength++;
    
    // Reset all bars
    strengthBars.forEach(bar => {
        bar.classList.remove('weak', 'fair', 'good', 'strong');
    });
    
    // Update strength bars and text
    if (password.length === 0) {
        if (strengthText) {
            strengthText.innerHTML = 'Password strength: <span class="font-semibold">Not set</span>';
        }
    } else {
        let strengthClass = '';
        let strengthLabel = '';
        
        if (strength === 1) {
            strengthClass = 'weak';
            strengthLabel = 'Weak';
        } else if (strength === 2) {
            strengthClass = 'fair';
            strengthLabel = 'Fair';
        } else if (strength === 3) {
            strengthClass = 'good';
            strengthLabel = 'Good';
        } else if (strength === 4) {
            strengthClass = 'strong';
            strengthLabel = 'Strong';
        }
        
        for (let i = 0; i < strength; i++) {
            strengthBars[i].classList.add(strengthClass);
        }
        
        if (strengthText) {
            strengthText.innerHTML = `Password strength: <span class="font-semibold">${strengthLabel}</span>`;
        }
    }
}

function updateRequirement(id, met) {
    const element = document.getElementById(id);
    if (!element) return;
    
    if (met) {
        element.classList.remove('text-slate-300');
        element.classList.add('text-emerald-500');
    } else {
        element.classList.remove('text-emerald-500');
        element.classList.add('text-slate-300');
    }
}
