let currentStep = 1;
const totalSteps = 4;
let selectedRoles = [];

document.addEventListener('DOMContentLoaded', function() {
    // Initialize selected roles from existing input state
    const existingRoleInputs = document.querySelectorAll('input[name*="roles"]:checked');
    if (existingRoleInputs.length > 0) {
        existingRoleInputs.forEach(input => {
            const role = input.value;
            const roleCard = document.querySelector(`.role-card[data-role="${role}"]`);
            if (roleCard) {
                roleCard.classList.add('selected');
            }
            if (!selectedRoles.includes(role)) {
                selectedRoles.push(role);
            }
        });
        updateRolesPreview();
    }

    // Initialize password strength checker
    const passwordInput = document.getElementById('passwordInput');
    if (passwordInput) {
        passwordInput.addEventListener('input', checkPasswordStrength);
    }
    
    // Initialize form submission handler
    const userForm = document.getElementById('userForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (userForm && submitBtn) {
        userForm.addEventListener('submit', function(e) {
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
        username: 'previewUsername',
        displayName: 'previewDisplayName',
        email: 'previewEmail'
    };

    const target = document.getElementById(elements[type]);
    if (!target) return;

    if (!val || val.trim() === '') {
        const defaults = {
            username: '@username',
            displayName: 'New User',
            email: 'email@example.com'
        };
        target.innerText = defaults[type];
    } else {
        if (type === 'username') {
            target.innerText = `@${val}`;
        } else {
            target.innerText = val;
        }
    }
}

function toggleRole(element, role) {
    if (!element || !role) return;
    
    element.classList.toggle('selected');
    
    // Get all checkboxes for the roles field
    const checkboxes = document.querySelectorAll('input[name*="roles"]');
    
    // Find the checkbox for this specific role
    checkboxes.forEach(checkbox => {
        if (checkbox.value === role) {
            checkbox.checked = element.classList.contains('selected');
        }
    });
    
    // Update selected roles array
    if (element.classList.contains('selected')) {
        if (!selectedRoles.includes(role)) {
            selectedRoles.push(role);
        }
    } else {
        selectedRoles = selectedRoles.filter(r => r !== role);
    }
    
    // Update preview
    updateRolesPreview();
}

function updateRolesPreview() {
    const previewRoles = document.getElementById('previewRoles');
    if (!previewRoles) return;
    
    if (selectedRoles.length === 0) {
        previewRoles.innerHTML = 'No roles assigned';
        previewRoles.className = 'px-3 py-1 bg-white/80 backdrop-blur-sm rounded-full text-xs font-semibold text-slate-600 border border-slate-200';
    } else {
        const roleLabels = selectedRoles.map(role => {
            if (role === 'ROLE_ADMIN') {
                return '<span class="px-3 py-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full text-xs font-semibold">Admin</span>';
            } else if (role === 'ROLE_STAFF') {
                return '<span class="px-3 py-1 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-full text-xs font-semibold">Staff</span>';
            } else if (role === 'ROLE_CUSTOMER') {
                return '<span class="px-3 py-1 bg-gradient-to-r from-emerald-500 to-lime-500 text-white rounded-full text-xs font-semibold">Customer</span>';
            }
            return '';
        }).join(' ');
        
        previewRoles.innerHTML = roleLabels;
        previewRoles.className = 'flex flex-wrap gap-2 justify-center';
    }
}

function setStatus(element, isActive) {
    if (!element) return;
    
    // Remove active class from all status cards
    document.querySelectorAll('.status-card').forEach(card => {
        card.classList.remove('active');
    });
    
    // Add active class to selected card
    element.classList.add('active');
    
    // Update the hidden radio buttons
    const radioButtons = document.querySelectorAll('input[name*="isActive"]');
    radioButtons.forEach(radio => {
        if ((radio.value === '1' && isActive) || (radio.value === '0' && !isActive)) {
            radio.checked = true;
        }
    });
    
    // Update preview
    const previewStatus = document.getElementById('previewStatus');
    if (previewStatus) {
        if (isActive) {
            previewStatus.className = 'px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl text-xs font-bold uppercase tracking-wider';
            previewStatus.innerHTML = 'Active';
        } else {
            previewStatus.className = 'px-4 py-2 bg-red-100 text-red-700 rounded-xl text-xs font-bold uppercase tracking-wider';
            previewStatus.innerHTML = 'Inactive';
        }
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
    const previewPassword = document.getElementById('previewPassword');
    
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
        if (previewPassword) {
            previewPassword.textContent = 'Not set';
            previewPassword.className = 'font-semibold text-slate-400';
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
        
        if (previewPassword) {
            previewPassword.textContent = '••••••••';
            if (strength >= 3) {
                previewPassword.className = 'font-semibold text-emerald-600';
            } else {
                previewPassword.className = 'font-semibold text-amber-600';
            }
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
