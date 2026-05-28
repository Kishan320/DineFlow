<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
      <button @click="$emit('back')" class="p-1.5 rounded-lg hover:bg-muted transition-colors" style="color:var(--muted-foreground)">
        <ArrowLeftIcon :size="16" />
      </button>
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">{{ item ? 'Edit Item' : 'Create Item' }}</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Items · {{ item ? 'Edit' : 'New' }}</p>
      </div>
    </div>

    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-5 py-4 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Manage Item Details</h2>
      </div>

      <form @submit.prevent="submit" class="p-5 space-y-6">

        <!-- Row 1: SKU + Item Name -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">SKU / Item Code <span style="color:var(--danger)">*</span></label>
            <input v-model="form.code" type="text" required placeholder="e.g. 00" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Item Name <span style="color:var(--danger)">*</span></label>
            <input v-model="form.itemName" type="text" required placeholder="Enter item name" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
          </div>
        </div>

        <!-- Row 2: Category + Prices -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-5">
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Category</label>
            <select v-model="form.category" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)">
              <option value="">Select an option</option>
              <option v-for="c in categoryOptions" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Restaurant Sales Price <span style="color:var(--danger)">*</span></label>
            <input v-model.number="form.restaurantPrice" type="number" step="0.01" min="0" required placeholder="0.00" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Bar Sales Price <span style="color:var(--danger)">*</span></label>
            <input v-model.number="form.barPrice" type="number" step="0.01" min="0" required placeholder="0.00" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Room Sales Price <span style="color:var(--danger)">*</span></label>
            <input v-model.number="form.roomPrice" type="number" step="0.01" min="0" required placeholder="0.00" class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)" />
          </div>
        </div>

        <!-- Row 3: Tax Type + Tax -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Tax Type <span style="color:var(--danger)">*</span></label>
            <select v-model="form.taxType" required class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)">
              <option value="Exclusive">Exclusive</option>
              <option value="Inclusive">Inclusive</option>
            </select>
          </div>
          <div>
            <label class="block text-xs font-medium mb-1.5" style="color:var(--foreground)">Tax <span style="color:var(--danger)">*</span></label>
            <select v-model="form.tax" required class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)">
              <option value="">Select an option</option>
              <option v-for="t in taxOptions" :key="t" :value="t">{{ t }}</option>
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
            <select v-model="form.itemType" required class="w-full border rounded-lg px-3 py-2 text-sm outline-none" style="background:var(--background);border-color:var(--border);color:var(--foreground)">
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
          <button type="submit" class="px-6 py-2 rounded-lg text-sm font-medium transition-colors" style="background:var(--primary);color:var(--primary-foreground)">
            {{ item ? 'Update' : 'Save' }}
          </button>
          <button type="button" @click="$emit('back')" class="px-6 py-2 rounded-lg text-sm font-medium border transition-colors hover:bg-muted" style="border-color:var(--border);color:var(--foreground)">Cancel</button>
        </div>

      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import { ArrowLeft as ArrowLeftIcon, ImageOff as ImageOffIcon, Trash2 as Trash2Icon } from '@lucide/vue';

const props = defineProps({ item: { type: Object, default: null } });
const emit  = defineEmits(['back', 'save']);

const categoryOptions = [
  'New Menu June', 'Break Fast Combo', 'Hot Beverage', 'Briyani Chicken',
  'Briyani Egg', 'Briyani Mutton', 'Briyani Veg', 'Dosa', 'Starters',
  'Curries', 'Breads at its best', 'Desserts', 'Cold Beverages', 'Meals',
  'Ayyan Appetizers', 'Banquet', 'Break Fast', 'Chaat Items', 'Chinese',
];
const taxOptions = ['GST (5.00%)', 'GST (12.00%)', 'GST (18.00%)', 'GST (28.00%)', 'VAT (5.00%)', 'Tax Exempt'];

const blankForm = () => ({
  code: '', itemName: '', category: '', restaurantPrice: '', barPrice: '', roomPrice: '',
  taxType: 'Exclusive', tax: '', state: 'On Sale', itemType: 'Physical Item', note: '', imageUrl: '',
});
const form = reactive(blankForm());
const imagePreview = ref('');
const fileName     = ref('');
const fileInput    = ref(null);

watch(() => props.item, (i) => {
  if (i) {
    Object.assign(form, { code: i.code, itemName: i.itemName, category: i.category, restaurantPrice: i.restaurantPrice, barPrice: i.barPrice, roomPrice: i.roomPrice, taxType: i.taxType, tax: i.tax, state: i.state, itemType: i.itemType, note: i.note || '', imageUrl: i.imageUrl || '' });
    imagePreview.value = i.imageUrl || '';
  } else {
    Object.assign(form, blankForm());
    imagePreview.value = '';
    fileName.value = '';
  }
}, { immediate: true });

function onFileChange(e) {
  const file = e.target.files[0];
  if (!file) return;
  fileName.value = file.name;
  const reader = new FileReader();
  reader.onload = (ev) => { imagePreview.value = ev.target.result; form.imageUrl = ev.target.result; };
  reader.readAsDataURL(file);
}

function removeImage() {
  imagePreview.value = '';
  fileName.value = '';
  form.imageUrl = '';
  if (fileInput.value) fileInput.value.value = '';
}

function submit() {
  emit('save', { ...form });
}
</script>
