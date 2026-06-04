<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
      <button @click="router.push({ name: 'settings-items' })" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
        <ArrowLeftIcon :size="16" />
      </button>
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">{{ isEdit ? 'Edit Item' : 'Create Item' }}</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Items · {{ isEdit ? 'Edit' : 'New' }}</p>
      </div>
    </div>

    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-5 py-4 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Manage Item Details</h2>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="animate-spin" style="color:var(--primary)">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
        </div>
      </div>

      <form v-else @submit.prevent="submit" class="p-5 space-y-6">

        <!-- Row 1: SKU + Item Name -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">SKU / Item Code <span style="color:var(--danger)">*</span></label>
            <input v-model="form.code" type="text" required placeholder="e.g. 00" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Item Name <span style="color:var(--danger)">*</span></label>
            <input v-model="form.item_name" type="text" required placeholder="Enter item name" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
          </div>
        </div>

        <!-- Row 2: Category + Prices -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Category</label>
            <select v-model="form.category" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)">
              <option value="">Select an option</option>
              <option v-for="c in categoryOptions" :key="c.id" :value="c.category_name">{{ c.category_name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Restaurant Sales Price <span style="color:var(--danger)">*</span></label>
            <input v-model.number="form.restaurant_price" type="number" step="0.01" min="0" required placeholder="0.00" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Bar Sales Price <span style="color:var(--danger)">*</span></label>
            <input v-model.number="form.bar_price" type="number" step="0.01" min="0" required placeholder="0.00" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Room Sales Price <span style="color:var(--danger)">*</span></label>
            <input v-model.number="form.room_price" type="number" step="0.01" min="0" required placeholder="0.00" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
          </div>
        </div>

        <!-- Row 3: Tax Type + Tax -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Tax Type <span style="color:var(--danger)">*</span></label>
            <select v-model="form.tax_type" required class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)">
              <option value="Exclusive">Exclusive</option>
              <option value="Inclusive">Inclusive</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Tax <span style="color:var(--danger)">*</span></label>
            <select v-model="form.tax" required class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)">
              <option value="">Select an option</option>
              <option v-for="t in taxOptions" :key="t.id" :value="t.description">{{ t.description }} ({{ t.tax_percent }}%)</option>
            </select>
          </div>
        </div>

        <!-- Row 4: Item State + Item Type -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Item State <span style="color:var(--danger)">*</span></label>
            <select v-model="form.state" required class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)">
              <option value="On Sale">On Sale</option>
              <option value="Off Sale">Off Sale</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Item Type <span style="color:var(--danger)">*</span></label>
            <select v-model="form.item_type" required class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)">
              <option value="Physical Item">Physical Item</option>
              <option value="Digital Item">Digital Item</option>
              <option value="Service">Service</option>
            </select>
          </div>
        </div>

        <!-- Row 5: Image + Note -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <!-- Image upload -->
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Image</label>
            <div class="border rounded-lg p-3 space-y-3" style="border-color:var(--border)">
              <!-- Preview -->
              <div class="w-24 h-24 rounded-lg border flex flex-col items-center justify-center relative" style="background:var(--muted);border-color:var(--border)">
                <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover rounded-lg" />
                <template v-else>
                  <ImageOffIcon :size="24" style="color:var(--muted-foreground)" />
                  <p class="text-xs mt-1 text-center leading-tight" style="color:var(--muted-foreground)">NO IMAGE<br>AVAILABLE</p>
                </template>
                <button v-if="imagePreview" type="button" @click="removeImage" class="absolute top-1 right-1 p-0.5 rounded" style="background:var(--danger);color:#fff">
                  <Trash2Icon :size="10" />
                </button>
              </div>
              <!-- File input -->
              <div class="flex items-center gap-2">
                <input ref="fileInput" type="file" accept="image/*" class="hidden" @change="onFileChange" />
                <span class="flex-1 text-xs border rounded-lg px-2 py-1.5 truncate" style="border-color:var(--border);color:var(--muted-foreground);background:var(--muted)">
                  {{ fileName || 'No file selected' }}
                </span>
                <button type="button" @click="fileInput.click()" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors" style="background:var(--primary);color:var(--primary-foreground)">Browse</button>
              </div>
              <p class="text-xs" style="color:var(--muted-foreground)">File Size Max 150KB</p>
            </div>
          </div>

          <!-- Note -->
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Note</label>
            <textarea v-model="form.note" rows="6" placeholder="Additional notes..." class="w-full border rounded-lg px-3 py-2 text-sm outline-none resize-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)"></textarea>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-3 pt-2">
          <button type="submit" :disabled="submitting" class="px-6 py-2 rounded-lg text-sm font-medium transition-colors" style="background:var(--primary);color:var(--primary-foreground)">
            {{ submitting ? 'Processing...' : (isEdit ? 'Update' : 'Save') }}
          </button>
          <button type="button" @click="router.push({ name: 'settings-items' })" class="px-6 py-2 rounded-lg text-sm font-medium border transition-colors hover:bg-muted" style="border-color:var(--border);color:var(--foreground)">Cancel</button>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { ArrowLeft as ArrowLeftIcon, ImageOff as ImageOffIcon, Trash2 as Trash2Icon } from '@lucide/vue';
import { itemApi, categoryApi, taxApi } from '@/services/settingsApi';
import { toast } from 'vue-sonner';

const router = useRouter();
const route  = useRoute();

const isEdit = computed(() => !!route.params.id);

const categoryOptions = ref([]);
const taxOptions      = ref([]);

const blankForm = () => ({
  code: '', item_name: '', category: '', restaurant_price: '', bar_price: '', room_price: '',
  tax_type: 'Exclusive', tax: '', state: 'On Sale', item_type: 'Physical Item', note: '', image_url: '',
});

const form = reactive(blankForm());
const imagePreview = ref('');
const fileName     = ref('');
const fileInput    = ref(null);
const loading      = ref(false);
const submitting   = ref(false);

onMounted(async () => {
  const [catRes, taxRes] = await Promise.allSettled([
    categoryApi.listAll(),
    taxApi.listAll(),
  ]);
  if (catRes.status === 'fulfilled') categoryOptions.value = catRes.value?.data ?? [];
  if (taxRes.status === 'fulfilled') taxOptions.value      = taxRes.value?.data ?? [];
  if (taxRes.status === 'fulfilled') taxOptions.value      = taxRes.value?.data  ?? taxRes.value  ?? [];

  if (isEdit.value) {
    loading.value = true;
    try {
      const res = await itemApi.getOne(route.params.id);
      const item = res.data || res;
      Object.assign(form, item);
      imagePreview.value = item.image_url || '';
    } catch (e) {
      toast.error('Failed to load item');
    } finally {
      loading.value = false;
    }
  }
});

function onFileChange(e) {
  const file = e.target.files[0];
  if (!file) return;
  fileName.value = file.name;
  const reader = new FileReader();
  reader.onload = (ev) => { imagePreview.value = ev.target.result; form.image_url = ev.target.result; };
  reader.readAsDataURL(file);
}

function removeImage() {
  imagePreview.value = '';
  fileName.value = '';
  form.image_url = '';
  if (fileInput.value) fileInput.value.value = '';
}

async function submit() {
  submitting.value = true;
  try {
    const formData = new FormData();
    formData.append('code', form.code);
    formData.append('item_name', form.item_name);
    formData.append('category', form.category);
    formData.append('restaurant_price', form.restaurant_price);
    formData.append('bar_price', form.bar_price);
    formData.append('room_price', form.room_price);
    formData.append('tax_type', form.tax_type);
    formData.append('tax', form.tax);
    formData.append('state', form.state);
    formData.append('item_type', form.item_type);
    formData.append('note', form.note);
    
    if (fileInput.value?.files[0]) {
      formData.append('image', fileInput.value.files[0]);
    }

    if (isEdit.value) {
      await itemApi.update(route.params.id, formData);
      toast.success('Item updated successfully!');
    } else {
      await itemApi.create(formData);
      toast.success('Item created successfully!');
    }

    router.push({ name: 'settings-items' });
  } catch (e) {
    toast.error(e?.errors ? Object.values(e.errors).flat().join(' ') : 'Failed to save item');
  } finally {
    submitting.value = false;
  }
}
</script>
