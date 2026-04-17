@extends('layouts.app')

@section('title', 'Point Rules')

@section('content')
<div class="container-xxl py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Point Rules</h5>
            <button class="btn btn-primary btn-sm" onclick="openCreateModal()">+ Tambah Rule</button>
        </div>
        <div class="card-body">
            <!-- Alert Placeholder -->
            <div id="alertContainer"></div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>NAMA RULE</th>
                            <th>TARGET ROLE</th>
                            <th>KONDISI (TIPE)</th>
                            <th>NILAI (MENIT)</th>
                            <th>POIN (+/-)</th>
                            <th width="150" class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="6" class="text-center">Loading data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="dataForm" onsubmit="saveData(event)">
          <div class="modal-header">
            <h5 class="modal-title font-weight-bold" id="modalTitle">Tambah Rule</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <input type="hidden" id="rule_id">
              
              <div class="row g-3">
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Nama Rule</label>
                      <input type="text" id="rule_name" class="form-control" required>
                      <div class="invalid-feedback" id="err_rule_name"></div>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label fw-bold">Target Role</label>
                      <select id="target_role" class="form-select" required>
                          <option value="superadmin">Superadmin</option>
                          <option value="admin">Admin</option>
                          <option value="guru">Guru</option>
                          <option value="murid" selected>Murid</option>
                      </select>
                      <div class="invalid-feedback" id="err_target_role"></div>
                  </div>
                  <div class="col-md-4">
                      <label class="form-label fw-bold">Kondisi (Tipe)</label>
                      <select id="condition_type" class="form-select" required onchange="toggleTimeFields()">
                          <option value="TIME">TIME (Keterlambatan)</option>
                          <option value="ALPHA">ALPHA (Tidak Hadir)</option>
                      </select>
                      <div class="invalid-feedback" id="err_condition_type"></div>
                  </div>
                  <div class="col-md-4 time-field">
                      <label class="form-label fw-bold">Minimal (Menit)</label>
                      <input type="number" id="min_value" class="form-control" min="0">
                      <div class="invalid-feedback" id="err_min_value"></div>
                  </div>
                  <div class="col-md-4 time-field">
                      <label class="form-label fw-bold">Maksimal (Menit)</label>
                      <input type="number" id="max_value" class="form-control" placeholder="Boleh kosong">
                      <div class="invalid-feedback" id="err_max_value"></div>
                  </div>
                  <div class="col-md-12">
                      <label class="form-label fw-bold">Point Modifier (+/-)</label>
                      <input type="number" id="point_modifier" class="form-control" required placeholder="Contoh: -10 atau 5">
                      <div class="invalid-feedback" id="err_point_modifier"></div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="btnSave">Simpan</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
    const DATA_URL = "{{ route('point-rules.data') }}";
    // Basis URL digunakan utk Store/Update/Delete (karena resource controller maping routenya /point-rules)
    const BASE_URL = "{{ url('admin/point-rules') }}"; 
    const csrfToken = "{{ csrf_token() }}";
    let modalInstance = null;
    
    document.addEventListener("DOMContentLoaded", () => {
        const modalEl = document.getElementById('formModal');
        if(typeof bootstrap !== 'undefined') {
            modalInstance = new bootstrap.Modal(modalEl);
        }
        loadData();
    });

    function showAlert(msg, type="success") {
        const container = document.getElementById('alertContainer');
        container.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${msg}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    function toggleTimeFields() {
        const type = document.getElementById('condition_type').value;
        document.querySelectorAll('.time-field').forEach(el => {
            el.style.display = type === 'TIME' ? 'block' : 'none';
        });
    }

    // LOAD DATA
    function loadData() {
        fetch(DATA_URL, { headers: { "Accept": "application/json" } })
        .then(res => res.json())
        .then(res => {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            if(res.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Belum ada data point rules.</td></tr>';
                return;
            }
            res.forEach((item) => {
                let badgePoint = item.point_modifier > 0 
                  ? `<span class="text-success fw-bold">+${item.point_modifier}</span>` 
                  : `<span class="text-danger fw-bold">${item.point_modifier}</span>`;
                
                let condition = item.condition_type === 'TIME' 
                  ? `Telat ${item.min_value} - ${item.max_value || '∞'}` 
                  : `Alpha (Penuh)`;

                const tr = `<tr>
                    <td>${item.rule_name}</td>
                    <td><span class="badge bg-secondary">${item.target_role.charAt(0).toUpperCase() + item.target_role.slice(1)}</span></td>
                    <td><span class="badge bg-info text-dark">${item.condition_type}</span></td>
                    <td>${condition}</td>
                    <td>${badgePoint}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning" onclick="openEditModal(${item.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteData(${item.id})">Del</button>
                    </td>
                </tr>`;
                tbody.insertAdjacentHTML('beforeend', tr);
            });
        });
    }

    // OPEN CREATE
    function openCreateModal() {
        document.getElementById('dataForm').reset();
        document.getElementById('rule_id').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah Rule Baru';
        clearErrors();
        toggleTimeFields();
        if(modalInstance) modalInstance.show();
    }

    // OPEN EDIT
    function openEditModal(id) {
        clearErrors();
        fetch(BASE_URL + '/' + id, { headers: { "Accept": "application/json" } })
        .then(res => res.json())
        .then(data => {
            document.getElementById('rule_id').value = data.id;
            document.getElementById('rule_name').value = data.rule_name;
            document.getElementById('target_role').value = data.target_role;
            document.getElementById('condition_type').value = data.condition_type;
            document.getElementById('min_value').value = data.min_value;
            document.getElementById('max_value').value = data.max_value;
            document.getElementById('point_modifier').value = data.point_modifier;
            
            document.getElementById('modalTitle').innerText = 'Edit Point Rule';
            toggleTimeFields();
            if(modalInstance) modalInstance.show();
        });
    }

    // SAVE DATA (Create/Edit)
    function saveData(e) {
        e.preventDefault();
        clearErrors();
        const id = document.getElementById('rule_id').value;
        const url = id ? `${BASE_URL}/${id}` : BASE_URL;
        const method = id ? 'PUT' : 'POST';
        
        const payload = {
            rule_name: document.getElementById('rule_name').value,
            target_role: document.getElementById('target_role').value,
            condition_type: document.getElementById('condition_type').value,
            min_value: document.getElementById('min_value').value,
            max_value: document.getElementById('max_value').value,
            point_modifier: document.getElementById('point_modifier').value
        };

        fetch(url, {
            method: method,
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest" // Agar laravel mendeteksi ini ajax request
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if(!res.ok) {
                if(res.status === 422) showErrors(data.errors);
                else showAlert(data.message || 'Terjadi kesalahan sistem', 'danger');
                throw new Error('Validation failed');
            }
            return data;
        })
        .then(data => {
            if(modalInstance) modalInstance.hide();
            showAlert('Berhasil menyimpan data rule!', 'success');
            loadData();
        }).catch(err => console.log(err));
    }

    // DELETE DATA
    function deleteData(id) {
        if(confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            fetch(`${BASE_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success || data.message) {
                    showAlert('Berhasil menghapus data rule!', 'success');
                    loadData();
                }
            });
        }
    }

    // CLEAR & SHOW ERRORS
    function clearErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.innerText = '');
    }
    function showErrors(errors) {
        for (const [key, msgs] of Object.entries(errors)) {
            const input = document.getElementById(key);
            const errDiv = document.getElementById(`err_${key}`);
            if(input && errDiv) {
                input.classList.add('is-invalid');
                errDiv.innerText = msgs[0];
            }
        }
    }
</script>
@endsection