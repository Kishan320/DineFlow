<template>
  <ImageKitProvider :url-endpoint="imagekitUrl" :public-key="imagekitPublicKey" :authenticator="authenticator">
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
              <Select filter v-model="form.category" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" :options="categoryOptions" optionLabel="category_name" optionValue="category_name" placeholder="Select a Category" />
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
              <Select filter v-model="form.tax_type" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" :options="[{label: 'Exclusive', value: 'Exclusive'}, {label: 'Inclusive', value: 'Inclusive'}]" optionLabel="label" optionValue="value" />
            </div>
            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Tax <span style="color:var(--danger)">*</span></label>
              <Select filter v-model="form.tax" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" :options="taxOptions" optionLabel="description" optionValue="description" placeholder="Select a Tax" />
            </div>
          </div>

          <!-- Row 4: Item State + Item Type -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Item State <span style="color:var(--danger)">*</span></label>
              <Select filter v-model="form.state" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" :options="[{label: 'On Sale', value: 'On Sale'}, {label: 'Off Sale', value: 'Off Sale'}]" optionLabel="label" optionValue="value" />
            </div>
            <div>
              <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Item Type <span style="color:var(--danger)">*</span></label>
              <Select filter v-model="form.item_type" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" :options="[{label: 'Physical Item', value: 'Physical Item'}, {label: 'Digital Item', value: 'Digital Item'}, {label: 'Service', value: 'Service'}]" optionLabel="label" optionValue="value" />
            </div>
          </div>

          <!-- Row 5: Image + Note -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <!-- Image upload -->
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Image</label>
            <div class="border rounded-lg p-3 space-y-3" style="border-color:var(--border)">
              <!-- Preview -->
              <div class="w-24 h-24 rounded-lg border flex flex-col items-center justify-center relative overflow-hidden" style="background:var(--muted);border-color:var(--border)">
                <IKImage v-if="form.image_url" :src="form.image_url" class="w-full h-full object-cover" />
                <img v-else-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                <template v-else>
                  <ImageOffIcon :size="24" style="color:var(--muted-foreground)" />
                  <p class="text-xs mt-1 text-center leading-tight" style="color:var(--muted-foreground)">NO IMAGE<br>AVAILABLE</p>
                </template>
                <button v-if="form.image_url || imagePreview" type="button" @click="removeImage" class="absolute top-1 right-1 p-0.5 rounded z-10" style="background:var(--danger);color:#fff">
                  <Trash2Icon :size="10" />
                </button>
              </div>
              <!-- File input -->
              <div class="flex items-center gap-2 relative">
                <input
                  type="file"
                  accept="image/*"
                  ref="fileInputRef"
                  class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                  title=""
                  @change="handleFileUpload"
                />
                <span class="flex-1 text-xs border rounded-lg px-2 py-1.5 truncate" style="border-color:var(--border);color:var(--muted-foreground);background:var(--muted)">
                  {{ uploading ? 'Uploading...' : (fileName || 'No file selected') }}
                </span>
                <button type="button" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors pointer-events-none" style="background:var(--primary);color:var(--primary-foreground)">Browse</button>
              </div>
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
          <button type="submit" :disabled="submitting || uploading" class="px-6 py-2 rounded-lg text-sm font-medium transition-colors disabled:opacity-50" style="background:var(--primary);color:var(--primary-foreground)">
            {{ submitting ? 'Processing...' : (isEdit ? 'Update' : 'Save') }}
          </button>
          <button type="button" @click="router.push({ name: 'settings-items' })" class="px-6 py-2 rounded-lg text-sm font-medium border transition-colors hover:bg-muted" style="border-color:var(--border);color:var(--foreground)">Cancel</button>
        </div>

      </form>
    </div>
  </div>
  </ImageKitProvider>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { ArrowLeft as ArrowLeftIcon, ImageOff as ImageOffIcon, Trash2 as Trash2Icon } from '@lucide/vue';
import { itemApi, categoryApi, taxApi } from '@/services/settingsApi';
import { toast } from 'vue-sonner';
import { ImageKitProvider, Image as IKImage, upload } from '@imagekit/vue';
import api from '@/services/axiosInstance';

const router = useRouter();
const route  = useRoute();

const imagekitUrl = import.meta.env.VITE_IMAGEKIT_URL_ENDPOINT || 'https://ik.imagekit.io/dineflowimages';
const imagekitPublicKey = import.meta.env.VITE_IMAGEKIT_PUBLIC_KEY || 'public_pD9H64ZwHoNJiHtBSQcedPGbiJw=';

const authenticator = async () => {
  try {
    const response = await api.get('/imagekit/auth');
    return response; // axiosInstance already unwraps response.data
  } catch (error) {
    console.error('ImageKit auth error:', error);
    throw new Error(`Authentication request failed: ${error.message}`);
  }
};

const isEdit = computed(() => !!route.params.id);

const categoryOptions = ref([]);
const taxOptions      = ref([]);

const blankForm = () => ({
  code: '', item_name: '', category: '', restaurant_price: '', bar_price: '', room_price: '',
  tax_type: 'Exclusive', tax: '', state: 'On Sale', item_type: 'Physical Item', note: '', image_url: '',
});

const form = reactive(blankForm());
const fileName     = ref('');
const loading      = ref(false);
const submitting   = ref(false);
const uploading    = ref(false);
const fileInputRef = ref(null);
const imagePreview = ref('');

onMounted(async () => {
  const [catRes, taxRes] = await Promise.allSettled([
    categoryApi.listAll(),
    taxApi.listAll(),
  ]);
  if (catRes.status === 'fulfilled') categoryOptions.value = catRes.value?.data ?? [];
  if (taxRes.status === 'fulfilled') taxOptions.value      = taxRes.value?.data ?? [];

  if (isEdit.value) {
    loading.value = true;
    try {
      const res = await itemApi.getOne(route.params.id);
      const item = res.data || res;
      Object.assign(form, item);
      if (item.image_url) {
        fileName.value = item.image_url.split('/').pop() || 'Existing image';
      }
    } catch (e) {
      toast.error('Failed to load item');
    } finally {
      loading.value = false;
    }
  }
});

async function handleFileUpload(e) {
  const file = e.target.files[0];
  if (!file) return;

  fileName.value = file.name;
  
  // Local preview
  const reader = new FileReader();
  reader.onload = (ev) => { imagePreview.value = ev.target.result; };
  reader.readAsDataURL(file);

  uploading.value = true;
  try {
    const authData = await authenticator();
    const result = await upload({
      file: file,
      fileName: file.name,
      publicKey: imagekitPublicKey,
      token: authData.token,
      signature: authData.signature,
      expire: authData.expire,
    });
    
    form.image_url = result.url;
    toast.success('Image uploaded successfully');
  } catch (err) {
    console.error(err);
    toast.error('Image upload failed');
    imagePreview.value = '';
    fileName.value = '';
    if (fileInputRef.value) fileInputRef.value.value = '';
  } finally {
    uploading.value = false;
  }
}

function removeImage() {
  fileName.value = '';
  form.image_url = '';
  imagePreview.value = '';
  if (fileInputRef.value) fileInputRef.value.value = '';
}

async function submit() {
  submitting.value = true;
  try {
    if (isEdit.value) {
      await itemApi.update(route.params.id, form);
      toast.success('Item updated successfully!');
    } else {
      await itemApi.create(form);
      toast.success('Item created successfully!');
    }

    router.push({ name: 'settings-items' });
  } catch (e) {
    toast.error(e?.response?.data?.message || 'Failed to save item');
  } finally {
    submitting.value = false;
  }
}
</script>
