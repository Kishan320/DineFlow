<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Roles & Permissions</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Administration · Roles</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      <!-- Roles list -->
      <div class="border rounded-xl" style="background:var(--card);border-color:var(--border)">
        <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color:var(--border)">
          <h2 class="text-sm font-semibold" style="color:var(--foreground)">Roles</h2>
          <button v-if="can('roles.manage')" @click="openCreateRole"
            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
            style="background:var(--primary);color:var(--primary-foreground)">
            <PlusIcon :size="12" /> New Role
          </button>
        </div>

        <div v-if="rolesLoading" class="flex justify-center py-8">
          <div class="spinner-sm" />
        </div>
        <div v-else class="p-2 space-y-1">
          <button
            v-for="role in roles" :key="role.id"
            @click="selectRole(role)"
            :class="['w-full text-left flex items-center justify-between px-3 py-2.5 rounded-lg transition-colors text-sm', selectedRole?.id === role.id ? 'bg-primary text-white' : 'hover:bg-muted']"
            :style="selectedRole?.id === role.id ? '' : 'color:var(--foreground)'"
          >
            <div>
              <p class="font-semibold text-sm">{{ role.name }}</p>
              <p class="text-xs mt-0.5" :style="selectedRole?.id === role.id ? 'opacity:.75' : 'color:var(--muted-foreground)'">
                {{ role.permissions_count }} permissions
              </p>
            </div>
            <ChevronRightIcon :size="14" :style="selectedRole?.id === role.id ? 'opacity:.75' : 'color:var(--muted-foreground)'" />
          </button>
        </div>
      </div>

      <!-- Permissions editor -->
      <div class="lg:col-span-2 border rounded-xl" style="background:var(--card);border-color:var(--border)">
        <div v-if="!selectedRole" class="flex flex-col items-center justify-center h-64 gap-3">
          <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:var(--muted)">
            <ShieldIcon :size="24" style="color:var(--muted-foreground)" />
          </div>
          <p class="text-sm font-semibold" style="color:var(--foreground)">Select a role</p>
          <p class="text-xs" style="color:var(--muted-foreground)">Click a role on the left to view and edit its permissions.</p>
        </div>

        <template v-else>
          <div class="px-4 py-3 border-b flex items-center justify-between" style="border-color:var(--border)">
            <div>
              <h3 class="text-sm font-semibold" style="color:var(--foreground)">{{ selectedRole.name }}</h3>
              <p class="text-xs" style="color:var(--muted-foreground)">{{ checkedPermissions.size }} of {{ totalPermissions }} permissions enabled</p>
            </div>
            <div class="flex items-center gap-2">
              <button v-if="can('roles.manage') && !isSystemRole" @click="openDeleteRole(selectedRole)"
                class="p-1.5 rounded-lg transition-colors hover:bg-muted" style="color:var(--danger)">
                <Trash2Icon :size="14" />
              </button>
              <button v-if="can('roles.manage')" @click="savePermissions" :disabled="saving"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                style="background:var(--primary);color:var(--primary-foreground)">
                <SaveIcon :size="12" />
                {{ saving ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </div>

          <div v-if="permsLoading" class="flex justify-center py-8">
            <div class="spinner-sm" />
          </div>
          <div v-else class="p-4 space-y-4 overflow-y-auto max-h-[600px]">
            <div v-for="(perms, module) in allPermissionsGrouped" :key="module">
              <div class="flex items-center justify-between mb-2">
                <p class="text-xs font-bold uppercase tracking-wider" style="color:var(--muted-foreground)">{{ module }}</p>
                <button @click="toggleModule(module, perms)" class="text-xs font-semibold" style="color:var(--primary)" :disabled="!can('roles.manage')">
                  {{ isModuleFullyChecked(perms) ? 'Deselect All' : 'Select All' }}
                </button>
              </div>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                <label v-for="perm in perms" :key="perm"
                  :class="['flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer border transition-all text-xs',
                    checkedPermissions.has(perm)
                      ? 'border-primary bg-primary/10'
                      : 'border-border hover:border-muted-foreground'
                  ]"
                  :style="checkedPermissions.has(perm) ? 'border-color:var(--primary);background:color-mix(in srgb,var(--primary) 10%,transparent)' : 'border-color:var(--border)'">
                  <input type="checkbox"
                    :checked="checkedPermissions.has(perm)"
                    :disabled="!can('roles.manage') || selectedRole.name === 'Super Admin'"
                    @change="togglePerm(perm)"
                    class="accent-primary w-3.5 h-3.5 flex-shrink-0"
                  />
                  <span :style="checkedPermissions.has(perm) ? 'color:var(--primary)' : 'color:var(--foreground)'">
                    {{ perm.split('.').slice(1).join('.') || perm }}
                  </span>
                </label>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>

    <!-- Create Role Modal -->
    <div v-if="showCreateModal" class="modal-overlay" @click.self="showCreateModal = false">
      <div class="modal-box" style="max-width:420px">
        <div class="modal-head">
          <h3 class="modal-title">Create New Role</h3>
          <button @click="showCreateModal = false" class="modal-close"><XIcon :size="18" /></button>
        </div>
        <div class="modal-body">
          <div class="field">
            <label class="field-lbl">Role Name *</label>
            <input v-model="newRoleName" class="field-inp" placeholder="e.g. Supervisor" required />
          </div>
          <div v-if="createError" class="error-box">{{ createError }}</div>
          <div class="flex gap-3">
            <button @click="showCreateModal = false" class="btn-secondary flex-1">Cancel</button>
            <button @click="createRole" :disabled="creatingRole || !newRoleName.trim()" class="btn-primary flex-1">
              {{ creatingRole ? 'Creating...' : 'Create Role' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Role Confirm -->
    <div v-if="deletingRole" class="modal-overlay" @click.self="deletingRole = null">
      <div class="modal-box" style="max-width:400px">
        <div class="modal-head">
          <h3 class="modal-title">Delete Role</h3>
          <button @click="deletingRole = null" class="modal-close"><XIcon :size="18" /></button>
        </div>
        <div class="modal-body">
          <p class="text-sm mb-4" style="color:var(--foreground)">
            Delete role <strong>{{ deletingRole.name }}</strong>? Users with this role will lose access.
          </p>
          <div class="flex gap-3">
            <button @click="deletingRole = null" class="btn-secondary flex-1">Cancel</button>
            <button @click="confirmDeleteRole" :disabled="deleting" class="btn-danger flex-1">
              {{ deleting ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import {
  Plus as PlusIcon, X as XIcon, ChevronRight as ChevronRightIcon,
  Shield as ShieldIcon, Trash2 as Trash2Icon, Save as SaveIcon,
} from '@lucide/vue';
import { adminApi } from '@/services/adminApi';
import { usePermission } from '@/composables/usePermission.js';
import { toast } from 'vue-sonner';

const { can } = usePermission();

const roles                = ref([]);
const rolesLoading         = ref(false);
const selectedRole         = ref(null);
const checkedPermissions   = ref(new Set());
const allPermissionsGrouped= ref({});
const permsLoading         = ref(false);
const saving               = ref(false);
const showCreateModal      = ref(false);
const newRoleName          = ref('');
const creatingRole         = ref(false);
const createError          = ref('');
const deletingRole         = ref(null);
const deleting             = ref(false);

const SYSTEM_ROLES = ['Super Admin', 'Admin'];
const isSystemRole = computed(() => SYSTEM_ROLES.includes(selectedRole.value?.name));

const totalPermissions = computed(() =>
  Object.values(allPermissionsGrouped.value).flat().length
);

async function loadRoles() {
  rolesLoading.value = true;
  try {
    const res = await adminApi.getRoles();
    roles.value = res.data;
  } catch { toast.error('Failed to load roles'); }
  finally { rolesLoading.value = false; }
}

async function loadAllPermissions() {
  const res = await adminApi.getAllPermissions();
  allPermissionsGrouped.value = res.data;
}

async function selectRole(role) {
  selectedRole.value = role;
  permsLoading.value = true;
  try {
    const res = await adminApi.getRolePermissions(role.id);
    checkedPermissions.value = new Set(res.data);
  } catch { toast.error('Failed to load permissions'); }
  finally { permsLoading.value = false; }
}

function togglePerm(perm) {
  if (checkedPermissions.value.has(perm)) {
    checkedPermissions.value.delete(perm);
  } else {
    checkedPermissions.value.add(perm);
  }
}

function isModuleFullyChecked(perms) {
  return perms.every(p => checkedPermissions.value.has(p));
}

function toggleModule(module, perms) {
  if (isModuleFullyChecked(perms)) {
    perms.forEach(p => checkedPermissions.value.delete(p));
  } else {
    perms.forEach(p => checkedPermissions.value.add(p));
  }
}

async function savePermissions() {
  if (!selectedRole.value) return;
  saving.value = true;
  try {
    await adminApi.syncRolePermissions(selectedRole.value.id, [...checkedPermissions.value]);
    toast.success('Permissions saved!');
    await loadRoles();
  } catch (e) {
    toast.error(e?.message || 'Failed to save');
  } finally { saving.value = false; }
}

function openCreateRole() {
  newRoleName.value = '';
  createError.value = '';
  showCreateModal.value = true;
}

async function createRole() {
  if (!newRoleName.value.trim()) return;
  creatingRole.value = true;
  createError.value = '';
  try {
    await adminApi.createRole({ name: newRoleName.value });
    toast.success('Role created!');
    showCreateModal.value = false;
    await loadRoles();
  } catch (e) {
    createError.value = e?.message || 'Failed to create role';
  } finally { creatingRole.value = false; }
}

function openDeleteRole(role) { deletingRole.value = role; }

async function confirmDeleteRole() {
  deleting.value = true;
  try {
    await adminApi.deleteRole(deletingRole.value.id);
    toast.success('Role deleted!');
    if (selectedRole.value?.id === deletingRole.value.id) {
      selectedRole.value = null;
    }
    deletingRole.value = null;
    await loadRoles();
  } catch (e) {
    toast.error(e?.message || 'Delete failed');
  } finally { deleting.value = false; }
}

onMounted(async () => {
  await Promise.all([loadRoles(), loadAllPermissions()]);
});
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
  color: var(--danger);
}
.btn-primary {
  padding: 10px; border-radius: 9px; border: none; cursor: pointer;
  background: var(--primary); color: var(--primary-foreground);
  font-size: 13px; font-weight: 700;
}
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
.spinner-sm {
  width: 24px; height: 24px; border-radius: 50%;
  border: 2.5px solid var(--border); border-top-color: var(--primary);
  animation: spin .7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
