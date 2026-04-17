<div class="mb-3">
    <label for="rule_name" class="form-label">Nama Rule <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('rule_name') is-invalid @enderror" id="rule_name" name="rule_name" value="{{ old('rule_name', $model->rule_name ?? '') }}" required placeholder="Contoh: Terlambat Masuk">
    @error('rule_name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="target_role" class="form-label">Target Role <span class="text-danger">*</span></label>
    <select class="form-select @error('target_role') is-invalid @enderror" id="target_role" name="target_role" required>
        <option value="" disabled {{ old('target_role', $model->target_role ?? '') == '' ? 'selected' : '' }}>Pilih Target Role</option>
        <option value="murid" {{ old('target_role', $model->target_role ?? '') == 'murid' ? 'selected' : '' }}>Murid</option>
        <option value="guru" {{ old('target_role', $model->target_role ?? '') == 'guru' ? 'selected' : '' }}>Guru</option>
        <option value="semua" {{ old('target_role', $model->target_role ?? '') == 'semua' ? 'selected' : '' }}>Semua</option>
    </select>
    @error('target_role')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="condition_operator" class="form-label">Operator Kondisi <span class="text-danger">*</span></label>
    <select class="form-select @error('condition_operator') is-invalid @enderror" id="condition_operator" name="condition_operator" required>
        <option value="" disabled {{ old('condition_operator', $model->condition_operator ?? '') == '' ? 'selected' : '' }}>Pilih Operator</option>
        <option value="<" {{ old('condition_operator', $model->condition_operator ?? '') == '<' ? 'selected' : '' }}>< (Kurang Dari)</option>
        <option value=">" {{ old('condition_operator', $model->condition_operator ?? '') == '>' ? 'selected' : '' }}>> (Lebih Dari)</option>
        <option value="BETWEEN" {{ old('condition_operator', $model->condition_operator ?? '') == 'BETWEEN' ? 'selected' : '' }}>BETWEEN (Di Antara)</option>
    </select>
    @error('condition_operator')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="condition_value" class="form-label">Nilai Kondisi <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('condition_value') is-invalid @enderror" id="condition_value" name="condition_value" value="{{ old('condition_value', $model->condition_value ?? '') }}" required placeholder="Contoh: 06:30:00 atau 15 (menit)">
    <div class="form-text">Bisa berupa waktu (06:30:00) atau angka integer (menit)</div>
    @error('condition_value')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="point_modifier" class="form-label">Point Modifier <span class="text-danger">*</span></label>
    <input type="number" class="form-control @error('point_modifier') is-invalid @enderror" id="point_modifier" name="point_modifier" value="{{ old('point_modifier', $model->point_modifier ?? '') }}" required placeholder="Contoh: -5">
    <div class="form-text">Gunakan minus (-) untuk pengurangan poin, atau positif untuk penambahan.</div>
    @error('point_modifier')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
