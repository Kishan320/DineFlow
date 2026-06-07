<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">User Management</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Administration · Users</p>
      </div>
      <button v-if="can('users.manage')" @click="openCreate"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        style="background:var(--primary);color:var(--primary-foreground)">
        <PlusIcon :size="15" /> Add User
      </button>
    </div>

    <!-- Users Table -->
    <div class="border rounded-xl" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Staff Members</h2>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-16">
        <div class="spinner-lg" />
      </div>

      <!-- Empty -->
      <div v-else-if="users.length === 0" class="flex flex-col items-center justify-center py-16 gap-3">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:var(--muted)">
          <UsersIcon :size="24" style="color:var(--muted-foreground)" />
        </div>
        <p class="text-sm font-semibold" style="color:var(--foreground)">No staff members yet</p>
        <p class="text-xs" style="color:var(--muted-foreground)">Click "Add User" to create the first staff account.</p>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr style="border-bottom:1px solid var(--border)">
              <th class="text-left px-4 py-3 text-xs font-semibold" style="color:var(--muted-foreground)">#</th>
              <th class="text-left px-4 py-3 text-xs font-semibold" style="color:var(--muted-foreground)">Name</th>
              <th class="text-left px-4 py-3 text-xs font-semibold" style="color:var(--muted-foreground)">Email</th>
              <th class="text-left px-4 py-3 text-xs font-semibold" style="color:var(--muted-foreground)">Role</th>
              <th class="text-left px-4 py-3 text-xs font-semibold" style="color:var(--muted-foreground)">Created</th>
              <th v-if="can('users.manage')" class="text-right px-4 py-3 text-xs font-semibold" style="color:var(--muted-foreground)">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(user, idx) in users" :key="user.id" style="border-bottom:1px solid var(--border)">
              <td class="px-4 py-3 text-xs" style="color:var(--muted-foreground)">{{ idx + 1 }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold"
                    style="background:var(--primary);color:var(--primary-foreground)">
                    {{ getInitials(user.name) }}
                  </div>
                  <span class="font-medium text-sm" style="color:var(--foreground)">{{ user.name }}</span>
                </div>
              </td>
              <td class="px-4 py-3 text-sm" style="color:var(--muted-foreground)">{{ user.email }}</td>
              <td class="px-4 py-3">
                <span v-for="role in user.roles" :key="role"
                  class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold mr-1"
                  :style="getRoleBadge(role)">
                  {{ role }}
                </span>
                <span v-if="user.roles.length === 0" class="text-xs" style="color:var(--muted-foreground)">No role</span>
              </td>
              <td class="px-4 py-3 text-xs" style="color:var(--muted-foreground)">
                {{ new Date(user.created_at).toLocaleDateString() }}
              </td>
              <td v-if="can('users.manage')" class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button @click="openEdit(user)" title="Edit"
                    class="p-1.5 rounded-lg transition-colors hover:bg-muted" style="color:var(--primary)">
                    <PencilIcon :size="14" />
                  </button>
                  <button @click="openDelete(user)" title="Delete"
                    class="p-1.5 rounded-lg transition-colors hover:bg-muted" style="color:var(--danger)">
                    <Trash2Icon :size="14" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create / Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-box">
        <div class="modal-head">
          <h3 class="modal-title">{{ editingUser ? 'Edit User' : 'Add New User' }}</h3>
          <button @click="closeModal" class="modal-close"><XIcon :size="18" /></button>
        </div>
        <form @submit.prevent="saveUser" class="modal-body">
          <div class="field">
            <label class="field-lbl">Full Name *</label>
            <input v-model="form.name" required class="field-inp" placeholder="John Doe" />
          </div>
          <div class="field">
            <label class="field-lbl">Email Address *</label>
            <input v-model="form.email" type="email" required class="field-inp" placeholder="john@example.com" />
          </div>
          <div class="field">
            <label class="field-lbl">{{ editingUser ? 'New Password (leave blank to keep)' : 'Password *' }}</label>
            <input v-model="form.password" type="password" :required="!editingUser" class="field-inp" placeholder="Min 8 characters" />
          </div>
          <div class="field">
            <label class="field-lbl">Role *</label>
            <select v-model="form.role" required class="field-inp">
              <option value="">Select a role...</option>
              <option v-for="role in availableRoles" :key="role.name" :value="role.name">{{ role.name }}</option>
            </select>
          </div>
          <div v-if="formError" class="error-box">{{ formError }}</div>
          <div class="flex gap-3 mt-2">
            <button type="button" @click="closeModal" class="btn-secondary flex-1">Cancel</button>
            <button type="submit" :disabled="saving" class="btn-primary flex-1">
              {{ saving ? 'Saving...' : (editingUser ? 'Update User' : 'Create User') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div v-if="deletingUser" class="modal-overlay" @click.self="deletingUser = null">
      <div class="modal-box" style="max-width:400px">
        <div class="modal-head">
          <h3 class="modal-title">Delete User</h3>
          <button @click="deletingUser = null" class="modal-close"><XIcon :size="18" /></button>
        </div>
        <div class="modal-body">
          <p class="text-sm mb-4" style="color:var(--foreground)">
            Are you sure you want to delete <strong>{{ deletingUser.name }}</strong>? This action cannot be undone.
          </p>
          <div class="flex gap-3">
            <button @click="deletingUser = null" class="btn-secondary flex-1">Cancel</button>
            <button @click="confirmDelete" :disabled="deleting" class="btn-danger flex-1">
              {{ deleting ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Plus as PlusIcon, X as XIcon, Users as UsersIcon, Pencil as PencilIcon, Trash2 as Trash2Icon } from '@lucide/vue';
import { adminApi } from '@/services/adminApi';
import { usePermission } from '@/composables/usePermission.js';
import { toast } from 'vue-sonner';

const { can } = usePermission();

const users          = ref([]);
const availableRoles = ref([]);
const loading        = ref(false);
const saving         = ref(false);
const deleting       = ref(false);
const showModal      = ref(false);
const editingUser    = ref(null);
const deletingUser   = ref(null);
const formError      = ref('');

const form = ref({ name: '', email: '', password: '', role: '' });

function getInitials(name) {
    return (name || '').split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
}

function getRoleBadge(role) {
    const map = {
        'Super Admin': 'background:color-mix(in srgb,#7c3aed 15%,transparent);color:#7c3aed',
        'Admin':       'background:color-mix(in srgb,var(--primary) 15%,transparent);color:var(--primary)',
        'Manager':     'background:color-mix(in srgb,#2563eb 15%,transparent);color:#2563eb',
        'Cashier':     'background:color-mix(in srgb,#16a34a 15%,transparent);color:#16a34a',
        'Kitchen Staff': 'background:color-mix(in srgb,#ea580c 15%,transparent);color:#ea580c',
        'Waiter':      'background:color-mix(in srgb,#0891b2 15%,transparent);color:#0891b2',
        'Delivery Staff': 'background:color-mix(in srgb,#d97706 15%,transparent);color:#d97706',
    };
    return map[role] || 'background:var(--muted);color:var(--muted-foreground)';
}

async function load() {
    loading.value = true;
    try {
        const [usersRes, rolesRes] = await Promise.all([
            adminApi.getUsers(),
            adminApi.getRoles(),
        ]);
        users.value          = usersRes.data;
        availableRoles.value = rolesRes.data;
    } catch {
        toast.error('Failed to load users');
    } finally {
        loading.value = false;
    }
}

function openCreate() {
    editingUser.value = null;
    form.value = { name: '', email: '', password: '', role: '' };
    formError.value = '';
    showModal.value = true;
}

function openEdit(user) {
    editingUser.value = user;
    form.value = { name: user.name, email: user.email, password: '', role: user.roles[0] || '' };
    formError.value = '';
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
    editingUser.value = null;
}

function openDelete(user) {
    deletingUser.value = user;
}

async function saveUser() {
    saving.value = true;
    formError.value = '';
    try {
        const payload = { ...form.value };
        if (!payload.password) delete payload.password;

        if (editingUser.value) {
            await adminApi.updateUser(editingUser.value.id, payload);
            toast.success('User updated successfully!');
        } else {
            await adminApi.createUser(payload);
            toast.success('User created successfully!');
        }
        closeModal();
        await load();
    } catch (e) {
        formError.value = e?.message || Object.values(e?.errors || {}).flat().join(' ') || 'Save failed';
    } finally {
        saving.value = false;
    }
}

async function confirmDelete() {
    deleting.value = true;
    try {
        await adminApi.deleteUser(deletingUser.value.id);
        toast.success('User deleted successfully!');
        deletingUser.value = null;
        await load();
    } catch (e) {
        toast.error(e?.message || 'Delete failed');
    } finally {
        deleting.value = false;
    }
}

onMounted(load);
</script>

<style scoped>
.modal-overlay {
    position: fixed; inset: 0; z-index: 100;
    background: rgba(0,0,0,.5); backdrop-filter: blur(3px);
    display: flex; align-items: center; justify-content: center; padding: 16px;
}
.modal-box {
    width: 100%; max-width: 480px; border-radius: 16px;
    background: var(--card); border: 1px solid var(--border);
    box-shadow: 0 20px 60px rgba(0,0,0,.3);
}
.modal-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px 16px; border-bottom: 1px solid var(--border);
}
.modal-title { font-size: 16px; font-weight: 700; color: var(--foreground); }
.modal-close {
    padding: 4px; border: none; background: none; cursor: pointer;
    color: var(--muted-foreground); border-radius: 8px; display: flex;
}
.modal-close:hover { background: var(--muted); }
.modal-body { padding: 20px 24px 24px; display: flex; flex-direction: column; gap: 14px; }
.field { display: flex; flex-direction: column; gap: 5px; }
.field-lbl { font-size: 11px; font-weight: 700; color: var(--muted-foreground); text-transform: uppercase; letter-spacing: .05em; }
.field-inp {
    padding: 9px 12px; border-radius: 8px; border: 1px solid var(--border);
    background: var(--input); color: var(--foreground); font-size: 13px; outline: none;
}
.field-inp:focus { border-color: var(--primary); }
.error-box {
    padding: 10px 12px; border-radius: 8px; font-size: 12px;
    background: color-mix(in srgb, var(--danger) 10%, transparent);
    color: var(--danger); border: 1px solid color-mix(in srgb, var(--danger) 30%, transparent);
}
.btn-primary {
    padding: 10px; border-radius: 9px; border: none; cursor: pointer;
    background: var(--primary); color: var(--primary-foreground);
    font-size: 13px; font-weight: 700; transition: opacity .15s;
}
.btn-primary:hover:not(:disabled) { opacity: .9; }
.btn-primary:disabled { opacity: .4; cursor: not-allowed; }
.btn-secondary {
    padding: 10px; border-radius: 9px; cursor: pointer;
    border: 1px solid var(--border); background: var(--muted);
    color: var(--foreground); font-size: 13px; font-weight: 600;
}
.btn-danger {
    padding: 10px; border-radius: 9px; border: none; cursor: pointer;
    background: var(--danger); color: #fff; font-size: 13px; font-weight: 700;
}
.btn-danger:disabled { opacity: .4; cursor: not-allowed; }
.spinner-lg {
    width: 32px; height: 32px; border-radius: 50%;
    border: 3px solid var(--border); border-top-color: var(--primary);
    animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
