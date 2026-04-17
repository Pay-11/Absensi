@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 font-weight-bold text-gray-800">Point Rules Management</h1>
        <button class="btn btn-primary" onclick="openCreateModal()">
            + Tambah Rule Baru
        </button>
    </div>

    <!-- Tabel Data -->
    <div class="card shadow mb-4 border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="dataTable" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Rule</th>
                            <th>Target Role</th>
                            <th>Tipe Kondisi</th>
                            <th>Min/Max (Menit)</th>
                            <th>Poin (+/-)</th>
                            <th class="text-center" width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <!-- Dimuat lewat AJAX -->
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
              
              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">Nama Rule</label>
                      <input type="text" id="rule_name" class="form-control" required>
                      <div class="invalid-feedback" id="err_rule_name"></div>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label class="form-label fw-bold">Target Role</label>
                      <select id="target_role" class="form-select" required>
                          <option value="superadmin">Superadmin</option>
                          <option value="admin">Admin</option>
                          <option value="guru">Guru</option>
                          <option value="murid" selected>Murid</option>
                      </select>
                      <div class="invalid-feedback" id="err_target_role"></div>
                  </div>
                  <div class="col-md-4 mb-3">
                      <label class="form-label fw-bold">Kondisi (Tipe)</label>
                      <select id="condition_type" class="form-select" required onchange="toggleTimeFields()">
                          <option value="TIME">TIME (Keterlambatan)</option>
                          <option value="ALPHA">ALPHA (Tidak Hadir)</option>
                      </select>
                      <div class="invalid-feedback" id="err_condition_type"></div>
                  </div>
                  <div class="col-md-4 mb-3 time-field">
                      <label class="form-label fw-bold">Minimal (Menit)</label>
                      <input type="number" id="min_value" class="form-control" min="0">
                      <div class="invalid-feedback" id="err_min_value"></div>
                  </div>
                  <div class="col-md-4 mb-3 time-field">
                      <label class="form-label fw-bold">Maksimal (Menit)</label>
                      <input type="number" id="max_value" class="form-control" placeholder="Kosong = Terus menerus">
                      <div class="invalid-feedback" id="err_max_value"></div>
                  </div>
                  <div class="col-md-12 mb-3">
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

<!-- Letakkan Script AJAX -->
<script>
    const API_URL = "{{ route('admin.point-rules.index') }}";
    const csrfToken = "{{ csrf_token() }}";
    let modalInstance = null;
    
    document.addEventListener("DOMContentLoaded", () => {
        // Init Bootstrap modal if using bs5
        modalInstance = new bootstrap.Modal(document.getElementById('formModal'));
        loadData();
    });

    function toggleTimeFields() {
        const type = document.getElementById('condition_type').value;
        document.querySelectorAll('.time-field').forEach(el => {
            el.style.display = type === 'TIME' ? 'block' : 'none';
        });
    }

    // LOAD DATA
    function loadData() {
        fetch(API_URL, {
            headers: { "X-Requested-With": "XMLHttpRequest" }
        })
        .then(res => res.json())
        .then(res => {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            res.data.forEach((item) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="fw-bold">${item.rule_name}</td>
                    <td><span class="badge bg-secondary">${item.target_role.toUpperCase()}</span></td>
                    <td><span class="badge bg-info text-dark">${item.condition_type}</span></td>
                    <td>${item.condition_type === 'TIME' ? (item.min_value + ' - ' + (item.max_value || '∞')) : 'N/A'}</td>
                    <td><span class="badge ${item.point_modifier > 0 ? 'bg-success' : 'bg-danger'}">${item.point_modifier > 0 ? '+' : ''}${item.point_modifier}</span></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning" onclick="openEditModal(${item.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteData(${item.id})">Hapus</button>
                    </td>
                `;
                tbody.appendChild(tr);
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
        modalInstance.show();
    }

    // OPEN EDIT
    function openEditModal(id) {
        clearErrors();
        fetch(API_URL + '/' + id, { headers: { "X-Requested-With": "XMLHttpRequest" } })
        .then(res => res.json())
        .then(res => {
            const data = res.data;
            document.getElementById('rule_id').value = data.id;
            document.getElementById('rule_name').value = data.rule_name;
            document.getElementById('target_role').value = data.target_role;
            document.getElementById('condition_type').value = data.condition_type;
            document.getElementById('min_value').value = data.min_value;
            document.getElementById('max_value').value = data.max_value;
            document.getElementById('point_modifier').value = data.point_modifier;
            
            document.getElementById('modalTitle').innerText = 'Edit Point Rule';
            toggleTimeFields();
            modalInstance.show();
        });
    }

    // SAVE DATA (Create/Edit)
    function saveData(e) {
        e.preventDefault();
        clearErrors();
        const id = document.getElementById('rule_id').value;
        const url = id ? `${API_URL}/${id}` : API_URL;
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
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify(payload)
        })
        .then(async res => {
            const data = await res.json();
            if(!res.ok) {
                if(res.status === 422) showErrors(data.errors);
                else alert(data.message || 'Terjadi kesalahan sistem');
                throw new Error('Validation failed');
            }
            return data;
        })
        .then(data => {
            modalInstance.hide();
            // Notifikasi ala alert, bs diganti sweetalert / toast
            alert(data.message);
            loadData();
        }).catch(err => console.log(err));
    }

    // DELETE DATA
    function deleteData(id) {
        if(confirm('Yakin ingin menghapus data ini?')) {
            fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    loadData();
                } else {
                    alert(data.message);
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
