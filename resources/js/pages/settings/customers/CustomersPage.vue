<template>
  <div class="p-4 lg:p-6 xl:p-8 max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold" style="color:var(--foreground)">Customers List</h1>
        <p class="text-sm mt-0.5" style="color:var(--muted-foreground)">Settings · Customers</p>
      </div>
      <button
        @click="openCreate"
        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        style="background:var(--primary);color:var(--primary-foreground)"
      >
        <PlusIcon :size="15" /> Add Customer
      </button>
    </div>

    <!-- Customers List -->
    <div class="border rounded-xl shadow-card" style="background:var(--card);border-color:var(--border)">
      <div class="px-4 py-3 border-b" style="border-color:var(--border)">
        <h2 class="text-sm font-semibold" style="color:var(--foreground)">Customers List</h2>
      </div>

      <!-- Controls -->
      <div class="flex items-center gap-3 px-4 py-3 border-b" style="border-color:var(--border)">
        <select v-model.number="pageSize" @change="page = 1" class="border rounded-lg px-2 py-1.5 text-xs outline-none" style="background:var(--muted);border-color:var(--border);color:var(--foreground)">
          <option v-for="n in [10, 25, 50]" :key="n" :value="n">{{ n }}</option>
        </select>
        <div class="flex items-center gap-2 rounded-lg px-3 py-1.5 flex-1 max-w-xs" style="background:var(--muted)">
          <SearchIcon :size="13" style="color:var(--muted-foreground)" />
          <input v-model="search" @input="page = 1" type="text" placeholder="Type to Search..." class="flex-1 bg-transparent text-xs outline-none" style="color:var(--foreground)" />
          <button v-if="search" @click="search = ''" style="color:var(--muted-foreground)"><XIcon :size="12" /></button>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto scrollbar-thin">
        <table class="w-full text-sm">
          <thead>
            <tr class="border-b" style="border-color:var(--border);background:color-mix(in srgb, var(--muted) 30%, transparent)">
              <th
                v-for="col in columns" :key="col.key"
                @click="toggleSort(col.key)"
                class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide whitespace-nowrap cursor-pointer select-none"
                style="color:var(--muted-foreground)"
              >
                <div class="flex items-center gap-1">
                  {{ col.label }}
                  <component :is="sortIcon(col.key)" :size="11" :style="sortKey === col.key ? 'color:var(--primary)' : 'color:var(--muted-foreground);opacity:0.4'" />
                </div>
              </th>
              <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color:var(--muted-foreground)">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="paginated.length === 0">
              <td :colspan="columns.length + 1" class="px-4 py-12 text-center text-sm" style="color:var(--muted-foreground)">No customer records found</td>
            </tr>
            <tr
              v-for="c in paginated" :key="c.id"
              class="border-b last:border-0 transition-colors hover:bg-muted/40"
              style="border-color:var(--border)"
            >
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs font-semibold" style="color:var(--foreground)">{{ c.code }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs font-medium" style="color:var(--foreground)">{{ c.companyName }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--foreground)">{{ c.contactPerson }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--foreground)">{{ c.email }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs tabular-nums" style="color:var(--foreground)">{{ c.mobile }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-xs" style="color:var(--muted-foreground)">{{ c.billingCity }}, {{ c.billingCountry }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center gap-1">
                  <button @click="openEdit(c)" class="p-1.5 rounded-lg hover:bg-info/10 transition-colors" style="color:var(--primary)" title="Edit">
                    <EditIcon :size="13" />
                  </button>
                  <button @click="openDelete(c)" class="p-1.5 rounded-lg hover:bg-danger/10 transition-colors" style="color:var(--danger)" title="Delete">
                    <Trash2Icon :size="13" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between gap-3 px-4 py-3 border-t" style="border-color:var(--border)">
        <span class="text-xs" style="color:var(--muted-foreground)">
          Showing {{ filtered.length === 0 ? 0 : (page - 1) * pageSize + 1 }} to {{ Math.min(page * pageSize, filtered.length) }} of {{ filtered.length }} entries
        </span>
        <div class="flex items-center gap-1">
          <button @click="page = Math.max(1, page - 1)" :disabled="page === 1" class="p-1.5 rounded-lg hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)">
            <ChevronLeftIcon :size="13" />
          </button>
          <button
            v-for="pn in pageNumbers" :key="pn" @click="page = pn"
            class="w-7 h-7 rounded-lg text-xs font-medium transition-colors"
            :style="page === pn ? 'background:var(--primary);color:var(--primary-foreground)' : 'color:var(--muted-foreground)'"
          >{{ pn }}</button>
          <button @click="page = Math.min(totalPages, page + 1)" :disabled="page === totalPages || totalPages === 0" class="p-1.5 rounded-lg hover:bg-muted disabled:opacity-40 disabled:cursor-not-allowed transition-colors" style="color:var(--muted-foreground)">
            <ChevronRightIcon :size="13" />
          </button>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <CustomerFormModal
      v-if="showFormModal"
      :customer="editingCustomer"
      @close="closeFormModal"
      @save="saveCustomer"
    />
    <CustomerDeleteModal
      v-if="deletingCustomer"
      :customer="deletingCustomer"
      @close="deletingCustomer = null"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import {
  Search as SearchIcon, X as XIcon, Plus as PlusIcon,
  Edit as EditIcon, Trash2 as Trash2Icon,
  ChevronLeft as ChevronLeftIcon, ChevronRight as ChevronRightIcon,
  ArrowUpDown, ArrowUp, ArrowDown,
} from '@lucide/vue';
import CustomerFormModal   from './CustomerFormModal.vue';
import CustomerDeleteModal from './CustomerDeleteModal.vue';

const search   = ref('');
const sortKey  = ref('code');
const sortDir  = ref('asc');
const page     = ref(1);
const pageSize = ref(10);

const showFormModal    = ref(false);
const editingCustomer  = ref(null);
const deletingCustomer = ref(null);

const customers = ref([
  { id: 1,  code: 'CUST-001', companyName: 'Spice Garden Pvt Ltd',    contactPerson: 'Arjun Mehta',    email: 'arjun@spicegarden.com',    mobile: '+919876543210', taxNumber: 'GST29ABCDE1234F1Z5', paymentTerms: 'Net 30', billingName: 'Arjun Mehta',    billingAddress: '12 MG Road',          billingAddress2: '',          billingCity: 'Bangalore',  billingState: 'Karnataka',     billingCountry: 'India', billingZip: '560001', sameAsBilling: true,  shippingName: '', shippingAddress: '', shippingAddress2: '', shippingCity: '', shippingState: '', shippingCountry: '', shippingZip: '', notes: 'Preferred customer. Bulk orders.' },
  { id: 2,  code: 'CUST-002', companyName: 'The Curry House',          contactPerson: 'Priya Sharma',   email: 'priya@curryhouse.in',      mobile: '+919823456781', taxNumber: 'GST27XYZAB5678G2H6', paymentTerms: 'Net 15', billingName: 'Priya Sharma',   billingAddress: '45 Linking Road',     billingAddress2: 'Floor 2',   billingCity: 'Mumbai',     billingState: 'Maharashtra',   billingCountry: 'India', billingZip: '400050', sameAsBilling: false, shippingName: 'Priya Sharma', shippingAddress: '45 Linking Road', shippingAddress2: '', shippingCity: 'Mumbai', shippingState: 'Maharashtra', shippingCountry: 'India', shippingZip: '400050', notes: '' },
  { id: 3,  code: 'CUST-003', companyName: 'Biryani Bros',             contactPerson: 'Karthik Reddy',  email: 'karthik@biryanibros.com',  mobile: '+919712345678', taxNumber: '',                    paymentTerms: 'COD',    billingName: 'Karthik Reddy',  billingAddress: '78 Jubilee Hills',    billingAddress2: '',          billingCity: 'Hyderabad',  billingState: 'Telangana',     billingCountry: 'India', billingZip: '500033', sameAsBilling: true,  shippingName: '', shippingAddress: '', shippingAddress2: '', shippingCity: '', shippingState: '', shippingCountry: '', shippingZip: '', notes: 'Catering orders only.' },
  { id: 4,  code: 'CUST-004', companyName: 'Dosa Palace',              contactPerson: 'Sneha Iyer',     email: 'sneha@dosapalace.com',     mobile: '+919654321098', taxNumber: 'GST33PQRST9012H3I7', paymentTerms: 'Net 45', billingName: 'Sneha Iyer',     billingAddress: '23 Anna Salai',       billingAddress2: 'Suite 5',   billingCity: 'Chennai',    billingState: 'Tamil Nadu',    billingCountry: 'India', billingZip: '600002', sameAsBilling: true,  shippingName: '', shippingAddress: '', shippingAddress2: '', shippingCity: '', shippingState: '', shippingCountry: '', shippingZip: '', notes: '' },
  { id: 5,  code: 'CUST-005', companyName: 'Tandoor Express',          contactPerson: 'Vijay Kumar',    email: 'vijay@tandoorexpress.in',  mobile: '+919543210987', taxNumber: '',                    paymentTerms: 'Net 30', billingName: 'Vijay Kumar',    billingAddress: '56 Connaught Place',  billingAddress2: '',          billingCity: 'New Delhi',  billingState: 'Delhi',         billingCountry: 'India', billingZip: '110001', sameAsBilling: false, shippingName: 'Vijay Kumar', shippingAddress: '10 Karol Bagh', shippingAddress2: '', shippingCity: 'New Delhi', shippingState: 'Delhi', shippingCountry: 'India', shippingZip: '110005', notes: 'VIP client.' },
  { id: 6,  code: 'CUST-006', companyName: 'Masala Junction',          contactPerson: 'Divya Nair',     email: 'divya@masalajunction.com', mobile: '+919432109876', taxNumber: 'GST32LMNOP3456J4K8', paymentTerms: 'Net 15', billingName: 'Divya Nair',     billingAddress: '89 MG Road',          billingAddress2: '',          billingCity: 'Kochi',      billingState: 'Kerala',        billingCountry: 'India', billingZip: '682001', sameAsBilling: true,  shippingName: '', shippingAddress: '', shippingAddress2: '', shippingCity: '', shippingState: '', shippingCountry: '', shippingZip: '', notes: '' },
  { id: 7,  code: 'CUST-007', companyName: 'Punjabi Dhaba Co.',        contactPerson: 'Suresh Singh',   email: 'suresh@punjabidhaba.com',  mobile: '+919321098765', taxNumber: '',                    paymentTerms: 'COD',    billingName: 'Suresh Singh',   billingAddress: '34 GT Road',          billingAddress2: '',          billingCity: 'Amritsar',   billingState: 'Punjab',        billingCountry: 'India', billingZip: '143001', sameAsBilling: true,  shippingName: '', shippingAddress: '', shippingAddress2: '', shippingCity: '', shippingState: '', shippingCountry: '', shippingZip: '', notes: 'Seasonal orders.' },
  { id: 8,  code: 'CUST-008', companyName: 'South Spice Eatery',       contactPerson: 'Meena Pillai',   email: 'meena@southspice.in',      mobile: '+919210987654', taxNumber: 'GST32UVWXY7890K5L9', paymentTerms: 'Net 30', billingName: 'Meena Pillai',   billingAddress: '67 Beach Road',       billingAddress2: 'Block B',   billingCity: 'Trivandrum', billingState: 'Kerala',        billingCountry: 'India', billingZip: '695001', sameAsBilling: true,  shippingName: '', shippingAddress: '', shippingAddress2: '', shippingCity: '', shippingState: '', shippingCountry: '', shippingZip: '', notes: '' },
  { id: 9,  code: 'CUST-009', companyName: 'Mughal Feast',             contactPerson: 'Rahul Ansari',   email: 'rahul@mughalfeast.com',    mobile: '+919109876543', taxNumber: '',                    paymentTerms: 'Net 60', billingName: 'Rahul Ansari',   billingAddress: '11 Hazratganj',       billingAddress2: '',          billingCity: 'Lucknow',    billingState: 'Uttar Pradesh', billingCountry: 'India', billingZip: '226001', sameAsBilling: false, shippingName: 'Rahul Ansari', shippingAddress: '22 Gomti Nagar', shippingAddress2: '', shippingCity: 'Lucknow', shippingState: 'Uttar Pradesh', shippingCountry: 'India', shippingZip: '226010', notes: 'Large event catering.' },
  { id: 10, code: 'CUST-010', companyName: 'Coastal Kitchen',          contactPerson: 'Lakshmi Rao',    email: 'lakshmi@coastalkitchen.in',mobile: '+919098765432', taxNumber: 'GST29ABCXY1122M6N0', paymentTerms: 'Net 30', billingName: 'Lakshmi Rao',    billingAddress: '5 Harbour Road',      billingAddress2: '',          billingCity: 'Visakhapatnam',billingState: 'Andhra Pradesh',billingCountry: 'India', billingZip: '530001', sameAsBilling: true,  shippingName: '', shippingAddress: '', shippingAddress2: '', shippingCity: '', shippingState: '', shippingCountry: '', shippingZip: '', notes: '' },
  { id: 11, code: 'CUST-011', companyName: 'Rajasthani Rasoi',         contactPerson: 'Amit Joshi',     email: 'amit@rajasthanirasoi.com', mobile: '+919876501234', taxNumber: '',                    paymentTerms: 'COD',    billingName: 'Amit Joshi',     billingAddress: '99 MI Road',          billingAddress2: '',          billingCity: 'Jaipur',     billingState: 'Rajasthan',     billingCountry: 'India', billingZip: '302001', sameAsBilling: true,  shippingName: '', shippingAddress: '', shippingAddress2: '', shippingCity: '', shippingState: '', shippingCountry: '', shippingZip: '', notes: 'Festival special orders.' },
  { id: 12, code: 'CUST-012', companyName: 'Bengal Bites',             contactPerson: 'Riya Banerjee',  email: 'riya@bengalbites.com',     mobile: '+919765432109', taxNumber: 'GST19DEFGH2233N7O1', paymentTerms: 'Net 15', billingName: 'Riya Banerjee',  billingAddress: '14 Park Street',      billingAddress2: 'Apt 3',     billingCity: 'Kolkata',    billingState: 'West Bengal',   billingCountry: 'India', billingZip: '700016', sameAsBilling: true,  shippingName: '', shippingAddress: '', shippingAddress2: '', shippingCity: '', shippingState: '', shippingCountry: '', shippingZip: '', notes: '' },
]);

const columns = [
  { key: 'code',          label: 'Code' },
  { key: 'companyName',   label: 'Company Name' },
  { key: 'contactPerson', label: 'Contact Person' },
  { key: 'email',         label: 'Email' },
  { key: 'mobile',        label: 'Mobile' },
  { key: 'billingCity',   label: 'Location' },
];

const filtered = computed(() => {
  let data = customers.value.filter(c =>
    !search.value ||
    c.code.toLowerCase().includes(search.value.toLowerCase()) ||
    c.companyName.toLowerCase().includes(search.value.toLowerCase()) ||
    c.contactPerson.toLowerCase().includes(search.value.toLowerCase()) ||
    c.email.toLowerCase().includes(search.value.toLowerCase())
  );
  if (sortKey.value) {
    data = [...data].sort((a, b) => {
      const av = a[sortKey.value], bv = b[sortKey.value];
      return sortDir.value === 'asc' ? String(av).localeCompare(String(bv)) : String(bv).localeCompare(String(av));
    });
  }
  return data;
});

const totalPages  = computed(() => Math.max(1, Math.ceil(filtered.value.length / pageSize.value)));
const paginated   = computed(() => filtered.value.slice((page.value - 1) * pageSize.value, page.value * pageSize.value));
const pageNumbers = computed(() => {
  const total = totalPages.value, cur = page.value, count = Math.min(5, total);
  const start = cur <= 3 ? 1 : cur >= total - 2 ? Math.max(1, total - 4) : cur - 2;
  return Array.from({ length: count }, (_, i) => start + i);
});

function toggleSort(key) {
  if (sortKey.value === key) sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
  else { sortKey.value = key; sortDir.value = 'asc'; }
}
function sortIcon(key) {
  if (sortKey.value !== key) return ArrowUpDown;
  return sortDir.value === 'asc' ? ArrowUp : ArrowDown;
}

function openCreate()         { editingCustomer.value = null; showFormModal.value = true; }
function openEdit(c)          { editingCustomer.value = c; showFormModal.value = true; }
function closeFormModal()     { showFormModal.value = false; editingCustomer.value = null; }
function openDelete(c)        { deletingCustomer.value = c; }

function saveCustomer(data) {
  if (editingCustomer.value) {
    const idx = customers.value.findIndex(c => c.id === editingCustomer.value.id);
    if (idx !== -1) customers.value[idx] = { ...customers.value[idx], ...data };
  } else {
    const nextNum = customers.value.length + 1;
    customers.value.push({ id: Date.now(), code: data.code || `CUST-${String(nextNum).padStart(3, '0')}`, ...data });
  }
  closeFormModal();
}

function confirmDelete() {
  customers.value = customers.value.filter(c => c.id !== deletingCustomer.value.id);
  deletingCustomer.value = null;
}
</script>
