let currentStep = 1;
const totalSteps = 4;

document.addEventListener('DOMContentLoaded', function () {
    initializeFileUpload();
    initializeFormSubmission();
    initializePasswordStrength();
});

/* ── Step Navigation ── */
function moveStep(delta) {
    const nextStep = currentStep + delta;
    if (nextStep < 1 || nextStep > totalSteps) return;

    document.querySelector(`[data-step="${currentStep}"]`).classList.remove('active');
    document.querySelector(`[data-step="${nextStep}"]`).classList.add('active');

    updateStepIndicators(currentStep, nextStep);
    currentStep = nextStep;

    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    prevBtn?.classList.toggle('opacity-0', currentStep === 1);
    prevBtn?.classList.toggle('pointer-events-none', currentStep === 1);

    if (currentStep === totalSteps) {
        nextBtn?.classList.add('hidden');
        submitBtn?.classList.remove('hidden');
    } else {
        nextBtn?.classList.remove('hidden');
        submitBtn?.classList.add('hidden');
    }
}

function updateStepIndicators(oldStep, newStep) {
    const oldPill = document.getElementById(`pill-${oldStep}`);
    const newPill = document.getElementById(`pill-${newStep}`);
    if (!oldPill || !newPill) return;

    if (newStep > oldStep) {
        oldPill.classList.remove('active');
        oldPill.classList.add('completed');
        oldPill.querySelector('span')?.classList.replace('text-indigo-600', 'text-emerald-600');
    } else {
        newPill.classList.remove('completed');
        newPill.querySelector('span')?.classList.replace('text-emerald-600', 'text-indigo-600');
    }
    newPill.classList.add('active');
}

/* ── Live Preview Sync ── */
function syncPreview(type, val) {
    const map = {
        displayName: 'previewDisplayName',
        email: 'previewEmail',
        bio: 'previewBio'
    };
    const target = document.getElementById(map[type]);
    if (!target) return;

    if (!val || val.trim() === '') {
        if (type === 'bio') target.innerText = 'No bio added yet.';
        return;
    }
    target.innerText = val;

    if (type === 'displayName') {
        const initial = document.getElementById('previewInitial');
        if (initial) initial.textContent = val.charAt(0).toUpperCase();
    }
}

/* ── Zodiac Selection ── */
function selectZodiac(element, sign) {
    document.querySelectorAll('.zodiac-card').forEach(c => c.classList.remove('selected'));
    element.classList.add('selected');

    const select = document.querySelector('select[name*="zodiacSign"]');
    if (select) select.value = sign;

    const symbols = {
        'Aries': '♈', 'Taurus': '♉', 'Gemini': '♊', 'Cancer': '♋',
        'Leo': '♌', 'Virgo': '♍', 'Libra': '♎', 'Scorpio': '♏',
        'Sagittarius': '♐', 'Capricorn': '♑', 'Aquarius': '♒', 'Pisces': '♓'
    };
    const sym = document.getElementById('previewZodiacSymbol');
    const name = document.getElementById('previewZodiacName');
    if (sym) sym.textContent = symbols[sign] || '—';
    if (name) name.textContent = sign;
}

/* ── File Upload ── */
function initializeFileUpload() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('profilePictureInput') || document.querySelector('input[type="file"]');
    if (!dropZone || !fileInput) return;

    dropZone.addEventListener('click', e => {
        if (!e.target.closest('button')) fileInput.click();
    });
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        if (e.dataTransfer.files.length) { fileInput.files = e.dataTransfer.files; handleFile(fileInput); }
    });
    fileInput.addEventListener('change', function () { handleFile(this); });
}

function handleFile(input) {
    const file = input.files[0];
    if (!file) return;

    const valid = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!valid.includes(file.type)) { alert('Please upload a valid image (JPEG, PNG, GIF, WebP)'); input.value = ''; return; }
    if (file.size > 10 * 1024 * 1024) { alert('File size must be less than 10MB'); input.value = ''; return; }

    const reader = new FileReader();
    reader.onload = e => {
        const imgResult = document.getElementById('imgResult');
        const dropZoneUI = document.getElementById('dropZoneUI');
        const previewContainer = document.getElementById('previewContainer');
        const previewAvatarContainer = document.getElementById('previewAvatarContainer');
        const previewInitial = document.getElementById('previewInitial');

        if (imgResult) imgResult.src = e.target.result;
        dropZoneUI?.classList.add('hidden');
        previewContainer?.classList.remove('hidden');

        if (previewAvatarContainer) {
            if (previewInitial) previewInitial.style.display = 'none';
            const existing = document.getElementById('previewAvatar');
            if (existing) {
                existing.src = e.target.result;
            } else {
                const img = document.createElement('img');
                img.id = 'previewAvatar';
                img.className = 'w-full h-full object-cover';
                img.src = e.target.result;
                previewAvatarContainer.innerHTML = '';
                previewAvatarContainer.appendChild(img);
            }
        }
    };
    reader.readAsDataURL(file);
}

function resetFile(event) {
    event.preventDefault();
    event.stopPropagation();

    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput) fileInput.value = '';

    const imgResult = document.getElementById('imgResult');
    const dropZoneUI = document.getElementById('dropZoneUI');
    const previewContainer = document.getElementById('previewContainer');
    const previewAvatarContainer = document.getElementById('previewAvatarContainer');

    if (imgResult) imgResult.src = '';
    dropZoneUI?.classList.remove('hidden');
    previewContainer?.classList.add('hidden');

    if (previewAvatarContainer) {
        const nameInput = document.querySelector('input[name*="displayName"]');
        const initial = nameInput?.value ? nameInput.value.charAt(0).toUpperCase() : 'U';
        previewAvatarContainer.innerHTML = `
            <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-4xl sm:text-5xl font-bold">
                <span id="previewInitial">${initial}</span>
            </div>`;
    }
}

/* ── Password ── */
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('toggleIcon');
    if (!input || !icon) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.classList.toggle('fa-eye', !isHidden);
    icon.classList.toggle('fa-eye-slash', isHidden);
}

function initializePasswordStrength() {
    const input = document.getElementById('passwordInput');
    if (!input) return;
    input.addEventListener('input', function () {
        const container = document.getElementById('passwordStrengthContainer');
        if (container) container.style.display = this.value.length > 0 ? 'block' : 'none';
        checkPasswordStrength();
    });
}

function checkPasswordStrength() {
    const password = document.getElementById('passwordInput')?.value || '';
    const bars = document.querySelectorAll('.strength-bar');
    const text = document.getElementById('strengthText');

    const reqs = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password)
    };

    updateRequirement('req-length', reqs.length);
    updateRequirement('req-uppercase', reqs.uppercase);
    updateRequirement('req-lowercase', reqs.lowercase);
    updateRequirement('req-number', reqs.number);

    const strength = Object.values(reqs).filter(Boolean).length;
    bars.forEach(b => b.classList.remove('weak', 'fair', 'good', 'strong'));

    const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];
    const classes = ['', 'weak', 'fair', 'good', 'strong'];
    for (let i = 0; i < strength; i++) bars[i]?.classList.add(classes[strength]);
    if (text) text.innerHTML = `Password strength: <span class="font-semibold">${labels[strength] || 'Not set'}</span>`;
}

function updateRequirement(id, met) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.toggle('text-slate-300', !met);
    el.classList.toggle('text-emerald-500', met);
}

/* ── Form Submit ── */
function initializeFormSubmission() {
    const form = document.getElementById('profileForm');
    const btn = document.getElementById('submitBtn');
    if (!form || !btn) return;
    form.addEventListener('submit', () => {
        const text = document.getElementById('submitBtnText');
        const loading = document.getElementById('submitBtnLoading');
        if (text && loading) {
            text.classList.add('hidden');
            loading.classList.remove('hidden');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        }
    });
}
