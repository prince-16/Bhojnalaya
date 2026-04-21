<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import OrderScreen from './pos/OrderScreen.vue'
import TableScreen from './pos/TableScreen.vue'
import TopBar from './pos/TopBar.vue'

const sections = ref([])
const tablesLoading = ref(false)
const tablesError = ref('')

const legendItems = [
  { label: 'Blank Table', color: '#eceff5' },
  { label: 'Running Table', color: '#47b7ff' },
  { label: 'Printed Table', color: '#76c35b' },
  { label: 'Paid Table', color: '#f1c67a' },
  { label: 'Running KOT Table', color: '#ffd24a' },
]

const quickActions = [
  { label: 'Delivery', emphasized: false },
  { label: 'Pick Up', emphasized: false },
  { label: '+ Add Table', emphasized: true },
]

const topMenu = [
  { label: 'Bills', icon: 'receipt' },
  { label: 'Orders', icon: 'bag' },
  { label: 'Tables', icon: 'table' },
  { label: 'Apps', icon: 'grid' },
  { label: 'Reports', icon: 'monitor' },
  { label: 'Customers', icon: 'users' },
  { label: 'Alerts', icon: 'bell' },
  { label: 'Profile', icon: 'user' },
]

const menuItems = ref([])
const menuItemsLoading = ref(false)
const menuItemsError = ref('')

const menuItemModalOpen = ref(false)
const menuItemModalMode = ref('create')
const menuItemSubmitting = ref(false)
const menuItemFormError = ref('')
const menuItemForm = reactive({
  id: null,
  name: '',
  category: '',
  short_code: '',
  item_type: 'Veg',
  price: 0,
  is_available: true,
})

const orderTypes = ['Dine In', 'Delivery', 'Pick Up']
const actionTabs = ['Table', 'Guest', 'Group', 'Notes', 'Order']
const paymentModes = ['Cash', 'Card', 'Due', 'Other', 'Part']

const view = ref('tables')
const selectedTable = ref(null)
const addTableModalOpen = ref(false)
const addTableSubmitting = ref(false)
const newTableForm = reactive({
  name: '',
  floor: '',
  capacity: 4,
  status: 'blank',
})
const selectedFloor = ref('');
const customFloor = ref('');

const uniqueFloors = computed(() => {
  const floors = new Set();
  sections.value.forEach(section => {
    if (section.title && section.title !== 'Unassigned') {
      floors.add(section.title);
    }
  });
  return Array.from(floors);
});

// Edit table modal
const editTableModalOpen = ref(false)
const editTableSubmitting = ref(false)
const editTableError = ref('')
const editTableForm = reactive({ id: null, name: '', floor: '', capacity: 4, status: 'blank' })
const editSelectedFloor = ref('')
const editCustomFloor = ref('')

// Delete confirm
const deleteConfirmOpen = ref(false)
const deleteSubmitting = ref(false)
const deleteError = ref('')
const deleteTargetTable = ref(null)
const selectedCategoryId = ref('')
const itemSearch = ref('')
const shortCode = ref('')
const selectedOrderType = ref('Dine In')
const selectedPaymentMode = ref('Cash')
const cart = reactive([])
const flags = reactive({
  complimentary: false,
  paid: false,
  loyalty: true,
  feedbackSms: true,
})

const categories = computed(() => {
  const categoryMap = new Map()

  for (const item of menuItems.value) {
    const categoryName = item.category?.trim() || 'Uncategorized'
    const categoryId = categoryName.toLowerCase().replaceAll(' ', '-')

    if (!categoryMap.has(categoryId)) {
      categoryMap.set(categoryId, {
        id: categoryId,
        name: categoryName,
        items: [],
      })
    }

    categoryMap.get(categoryId).items.push(item)
  }

  return [...categoryMap.values()].sort((left, right) => left.name.localeCompare(right.name))
})

const activeCategory = computed(() => {
  if (categories.value.length === 0) {
    return { id: '', name: 'Menu', items: [] }
  }

  return categories.value.find((category) => category.id === selectedCategoryId.value) ?? categories.value[0]
})

const filteredItems = computed(() => {
  const query = itemSearch.value.trim().toLowerCase()
  const codeQuery = shortCode.value.trim().toLowerCase()

  return activeCategory.value.items.filter((item) => {
    const searchTarget = `${item.name} ${item.id} ${item.short_code ?? ''}`.toLowerCase()

    if (codeQuery && String(item.short_code ?? '').toLowerCase() !== codeQuery) {
      return false
    }

    return searchTarget.includes(query)
  })
})

const totalAmount = computed(() => cart.reduce((sum, item) => sum + item.price * item.quantity, 0))

function normalizeTableStatus(status) {
  const allowedStatuses = new Set(['blank', 'running', 'printed', 'paid'])
  const normalized = String(status ?? '').trim().toLowerCase().replaceAll(' ', '-')

  return allowedStatuses.has(normalized) ? normalized : 'blank'
}

function mapTablesToSections(tables) {
  const sectionMap = new Map()

  for (const table of tables) {
    const floorTitle = table.floor?.trim() || 'Unassigned'

    if (!sectionMap.has(floorTitle)) {
      sectionMap.set(floorTitle, [])
    }

    sectionMap.get(floorTitle).push({
      id: table.id,
      label: table.name,
      status: normalizeTableStatus(table.status),
      apiTable: table,
    })
  }

  return [...sectionMap.entries()]
    .sort(([left], [right]) => left.localeCompare(right))
    .map(([title, mappedTables]) => ({
      title,
      tables: mappedTables.sort((left, right) => left.label.localeCompare(right.label)),
    }))
}

async function fetchTables() {
  tablesLoading.value = true
  tablesError.value = ''

  try {
    const response = await fetch('/api/tables')

    if (!response.ok) {
      throw new Error(`Unable to load tables (${response.status})`)
    }

    const tables = await response.json()
    sections.value = mapTablesToSections(tables)
  } catch (error) {
    tablesError.value = error instanceof Error ? error.message : 'Failed to load tables'
    sections.value = []
  } finally {
    tablesLoading.value = false
  }
}

function mapMenuItem(menuItem) {
  const accent = menuItem.item_type?.toLowerCase().includes('non') ? 'red' : 'green'

  return {
    ...menuItem,
    accent,
    price: Number(menuItem.price ?? 0),
  }
}

async function fetchMenuItems() {
  menuItemsLoading.value = true
  menuItemsError.value = ''

  try {
    const response = await fetch('/api/menu-items')

    if (!response.ok) {
      throw new Error(`Unable to load menu items (${response.status})`)
    }

    const data = await response.json()
    menuItems.value = data.map(mapMenuItem)

    if (!selectedCategoryId.value && categories.value.length > 0) {
      selectedCategoryId.value = categories.value[0].id
    } else if (!categories.value.some((category) => category.id === selectedCategoryId.value)) {
      selectedCategoryId.value = categories.value[0]?.id ?? ''
    }
  } catch (error) {
    menuItemsError.value = error instanceof Error ? error.message : 'Failed to load menu items'
    menuItems.value = []
  } finally {
    menuItemsLoading.value = false
  }
}

function openCreateMenuItemModal() {
  menuItemModalMode.value = 'create'
  menuItemForm.id = null
  menuItemForm.name = ''
  menuItemForm.category = activeCategory.value?.name === 'Menu' ? '' : activeCategory.value?.name ?? ''
  menuItemForm.short_code = ''
  menuItemForm.item_type = 'Veg'
  menuItemForm.price = 0
  menuItemForm.is_available = true
  menuItemFormError.value = ''
  menuItemModalOpen.value = true
}

async function openEditMenuItemModal(menuItem) {
  menuItemFormError.value = ''

  try {
    const response = await fetch(`/api/menu-items/${menuItem.id}`)

    if (!response.ok) {
      throw new Error(`Unable to load menu item (${response.status})`)
    }

    const fullItem = await response.json()

    menuItemModalMode.value = 'edit'
    menuItemForm.id = fullItem.id
    menuItemForm.name = fullItem.name ?? ''
    menuItemForm.category = fullItem.category ?? ''
    menuItemForm.short_code = fullItem.short_code ?? ''
    menuItemForm.item_type = fullItem.item_type ?? 'Veg'
    menuItemForm.price = Number(fullItem.price ?? 0)
    menuItemForm.is_available = Boolean(fullItem.is_available)
    menuItemModalOpen.value = true
  } catch (error) {
    menuItemsError.value = error instanceof Error ? error.message : 'Failed to load menu item details'
  }
}

function closeMenuItemModal() {
  if (!menuItemSubmitting.value) {
    menuItemModalOpen.value = false
  }
}

async function submitMenuItem() {
  const payload = {
    name: menuItemForm.name.trim(),
    category: menuItemForm.category.trim(),
    short_code: menuItemForm.short_code.trim() || null,
    item_type: menuItemForm.item_type,
    price: Number(menuItemForm.price),
    is_available: Boolean(menuItemForm.is_available),
  }

  if (!payload.name) {
    menuItemFormError.value = 'Item name is required'
    return
  }

  if (!payload.category) {
    menuItemFormError.value = 'Category is required'
    return
  }

  if (Number.isNaN(payload.price) || payload.price < 0) {
    menuItemFormError.value = 'Price must be 0 or greater'
    return
  }

  menuItemSubmitting.value = true
  menuItemFormError.value = ''

  try {
    const isEdit = menuItemModalMode.value === 'edit' && menuItemForm.id
    const url = isEdit ? `/api/menu-items/${menuItemForm.id}` : '/api/menu-items'
    const method = isEdit ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(payload),
    })

    if (!response.ok) {
      const data = await response.json().catch(() => ({}))
      throw new Error(data?.message ?? `Unable to save menu item (${response.status})`)
    }

    menuItemModalOpen.value = false
    await fetchMenuItems()
  } catch (error) {
    menuItemFormError.value = error instanceof Error ? error.message : 'Failed to save menu item'
  } finally {
    menuItemSubmitting.value = false
  }
}

async function deleteMenuItem(menuItem) {
  const confirmed = window.confirm(`Delete menu item ${menuItem.name}?`)

  if (!confirmed) {
    return
  }

  try {
    const response = await fetch(`/api/menu-items/${menuItem.id}`, {
      method: 'DELETE',
    })

    if (!response.ok) {
      throw new Error(`Unable to delete menu item (${response.status})`)
    }

    await fetchMenuItems()
  } catch (error) {
    menuItemsError.value = error instanceof Error ? error.message : 'Failed to delete menu item'
  }
}

function openAddTableModal() {
  newTableForm.name = ''
  newTableForm.floor = ''
  newTableForm.capacity = 4
  newTableForm.status = 'blank'
  selectedFloor.value = ''
  customFloor.value = ''
  tablesError.value = ''
  addTableModalOpen.value = true
}

function closeAddTableModal() {
  if (!addTableSubmitting.value) {
    addTableModalOpen.value = false
  }
}

async function submitCreateTable() {
  const capacity = Number.parseInt(String(newTableForm.capacity), 10)
  const status = normalizeTableStatus(newTableForm.status)
  const name = newTableForm.name.trim()
  let floor = ''
  if (selectedFloor.value === 'other') {
    floor = customFloor.value.trim()
  } else {
    floor = selectedFloor.value.trim()
  }

  if (!name) {
    tablesError.value = 'Table name is required'
    return
  }
  if (!Number.isInteger(capacity) || capacity < 1) {
    tablesError.value = 'Capacity must be a positive integer'
    return
  }
  if (!floor) {
    tablesError.value = 'Floor is required'
    return
  }

  tablesError.value = ''
  addTableSubmitting.value = true

  try {
    const response = await fetch('/api/tables', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        name,
        floor: floor || null,
        capacity,
        status,
        is_active: true,
      }),
    })

    if (!response.ok) {
      throw new Error(`Unable to create table (${response.status})`)
    }

    addTableModalOpen.value = false
    await fetchTables()
  } catch (error) {
    tablesError.value = error instanceof Error ? error.message : 'Failed to create table'
  } finally {
    addTableSubmitting.value = false
  }
}

function handleQuickAction(action) {
  if (action.label === '+ Add Table') {
    openAddTableModal()
  }
}

function openEditTableModal(table) {
  const apiTable = table.apiTable ?? table
  editTableForm.id = apiTable.id
  editTableForm.name = apiTable.name ?? table.label ?? ''
  editTableForm.capacity = apiTable.capacity ?? 4
  editTableForm.status = normalizeTableStatus(apiTable.status ?? table.status)
  const floor = apiTable.floor?.trim() ?? ''
  const existingFloors = uniqueFloors.value
  if (floor && existingFloors.includes(floor)) {
    editSelectedFloor.value = floor
    editCustomFloor.value = ''
  } else if (floor) {
    editSelectedFloor.value = 'other'
    editCustomFloor.value = floor
  } else {
    editSelectedFloor.value = ''
    editCustomFloor.value = ''
  }
  editTableError.value = ''
  editTableModalOpen.value = true
}

function closeEditTableModal() {
  if (!editTableSubmitting.value) {
    editTableModalOpen.value = false
  }
}

async function submitEditTable() {
  const name = editTableForm.name.trim()
  const capacity = Number.parseInt(String(editTableForm.capacity), 10)
  const status = normalizeTableStatus(editTableForm.status)
  let floor = ''
  if (editSelectedFloor.value === 'other') {
    floor = editCustomFloor.value.trim()
  } else {
    floor = editSelectedFloor.value.trim()
  }

  if (!name) { editTableError.value = 'Table name is required'; return }
  if (!Number.isInteger(capacity) || capacity < 1) { editTableError.value = 'Capacity must be a positive integer'; return }
  if (!floor) { editTableError.value = 'Floor is required'; return }

  editTableError.value = ''
  editTableSubmitting.value = true

  try {
    const response = await fetch(`/api/tables/${editTableForm.id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, floor: floor || null, capacity, status, is_active: true }),
    })
    if (!response.ok) {
      const data = await response.json().catch(() => ({}))
      throw new Error(data?.message ?? `Unable to update table (${response.status})`)
    }
    editTableModalOpen.value = false
    await fetchTables()
  } catch (error) {
    editTableError.value = error instanceof Error ? error.message : 'Failed to update table'
  } finally {
    editTableSubmitting.value = false
  }
}

function openDeleteConfirm(table) {
  deleteTargetTable.value = table.apiTable ?? table
  deleteError.value = ''
  deleteConfirmOpen.value = true
}

function closeDeleteConfirm() {
  if (!deleteSubmitting.value) {
    deleteConfirmOpen.value = false
    deleteTargetTable.value = null
  }
}

async function confirmDelete() {
  if (!deleteTargetTable.value) return
  deleteSubmitting.value = true
  deleteError.value = ''
  try {
    const response = await fetch(`/api/tables/${deleteTargetTable.value.id}`, { method: 'DELETE' })
    if (!response.ok) throw new Error(`Unable to delete table (${response.status})`)
    deleteConfirmOpen.value = false
    deleteTargetTable.value = null
    await fetchTables()
  } catch (error) {
    deleteError.value = error instanceof Error ? error.message : 'Failed to delete table'
  } finally {
    deleteSubmitting.value = false
  }
}

onMounted(() => {
  fetchTables()
  fetchMenuItems()
})

function iconPath(icon) {
  switch (icon) {
    case 'receipt':
      return 'M8 3.75A2.25 2.25 0 0 0 5.75 6v12.69c0 .7.81 1.09 1.36.67l1.34-1 1.34 1a.75.75 0 0 0 .9 0l1.34-1 1.34 1a.75.75 0 0 0 .9 0l1.34-1 1.34 1c.55.42 1.36.03 1.36-.67V6A2.25 2.25 0 0 0 16 3.75H8ZM8 5.25h8A.75.75 0 0 1 16.75 6v11.18l-.59-.44a.75.75 0 0 0-.9 0l-1.34 1-1.34-1a.75.75 0 0 0-.9 0l-1.34 1-1.34-1a.75.75 0 0 0-.9 0l-.59.44V6A.75.75 0 0 1 8 5.25Zm1.5 3a.75.75 0 0 1 .75-.75h3.5a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1-.75-.75Zm0 3a.75.75 0 0 1 .75-.75h3.5a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1-.75-.75Z'
    case 'bag':
      return 'M8.75 7.5V6a3.25 3.25 0 1 1 6.5 0v1.5h1.25A1.75 1.75 0 0 1 18.25 9.25v7.5A1.75 1.75 0 0 1 16.5 18.5h-9A1.75 1.75 0 0 1 5.75 16.75v-7.5A1.75 1.75 0 0 1 7.5 7.5h1.25Zm1.5 0h3.5V6a1.75 1.75 0 1 0-3.5 0v1.5Zm-2.75 1.5a.25.25 0 0 0-.25.25v7.5c0 .14.11.25.25.25h9a.25.25 0 0 0 .25-.25v-7.5a.25.25 0 0 0-.25-.25H7.5Z'
    case 'table':
      return 'M6.5 5.5A1.5 1.5 0 0 1 8 4h8a1.5 1.5 0 0 1 1.5 1.5V8A1.5 1.5 0 0 1 16 9.5h-.75v4.75a.75.75 0 0 1-1.5 0V13h-3.5v1.25a.75.75 0 0 1-1.5 0V9.5H8A1.5 1.5 0 0 1 6.5 8V5.5Zm1.5 0V8h8V5.5H8Zm-1.25 6.75a.75.75 0 0 1 .75-.75h9a.75.75 0 0 1 0 1.5h-9a.75.75 0 0 1-.75-.75Z'
    case 'grid':
      return 'M6.5 5.5A1.5 1.5 0 0 1 8 4h2a1.5 1.5 0 0 1 1.5 1.5v2A1.5 1.5 0 0 1 10 9H8a1.5 1.5 0 0 1-1.5-1.5v-2Zm6 0A1.5 1.5 0 0 1 14 4h2a1.5 1.5 0 0 1 1.5 1.5v2A1.5 1.5 0 0 1 16 9h-2a1.5 1.5 0 0 1-1.5-1.5v-2Zm-6 6A1.5 1.5 0 0 1 8 10h2a1.5 1.5 0 0 1 1.5 1.5v2A1.5 1.5 0 0 1 10 15H8a1.5 1.5 0 0 1-1.5-1.5v-2Zm6 0A1.5 1.5 0 0 1 14 10h2a1.5 1.5 0 0 1 1.5 1.5v2A1.5 1.5 0 0 1 16 15h-2a1.5 1.5 0 0 1-1.5-1.5v-2Z'
    case 'monitor':
      return 'M7.5 5A2.5 2.5 0 0 0 5 7.5v5A2.5 2.5 0 0 0 7.5 15h2.75v1.5H8.5a.75.75 0 0 0 0 1.5h7a.75.75 0 0 0 0-1.5h-1.75V15h2.75A2.5 2.5 0 0 0 19 12.5v-5A2.5 2.5 0 0 0 16.5 5h-9Zm0 1.5h9A1 1 0 0 1 17.5 7.5v5a1 1 0 0 1-1 1h-9a1 1 0 0 1-1-1v-5a1 1 0 0 1 1-1Z'
    case 'users':
      return 'M12 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Zm-4.75 7a4.75 4.75 0 0 1 9.5 0 .75.75 0 0 1-1.5 0 3.25 3.25 0 0 0-6.5 0 .75.75 0 0 1-1.5 0ZM6.5 11.25a2 2 0 1 0-1.97-2.33.75.75 0 0 1-1.48-.23 3.5 3.5 0 1 1 6.41 2.35.75.75 0 1 1-1.28-.79A1.98 1.98 0 0 0 6.5 11.25Zm11 0a2 2 0 1 0-1.68-.92.75.75 0 0 1-1.27.8 3.5 3.5 0 1 1 6.2-2.14.75.75 0 1 1-1.48.22 1.98 1.98 0 0 0-1.77 2.04Z'
    case 'bell':
      return 'M12 4.5a3.5 3.5 0 0 0-3.5 3.5v1.34c0 .4-.12.8-.34 1.13L7 12.1V13h10v-.9l-1.16-1.63a2 2 0 0 1-.34-1.13V8A3.5 3.5 0 0 0 12 4.5Zm-5 8.75v-1.15l.94-1.32c.4-.57.61-1.25.61-1.94V8a4.5 4.5 0 1 1 9 0v.84c0 .69.21 1.37.61 1.94l.94 1.32v1.15a.75.75 0 0 1-.75.75H15.8a3.05 3.05 0 0 1-5.6 0H7.75a.75.75 0 0 1-.75-.75Zm4.78 1.25a1.55 1.55 0 0 0 2.44 0h-2.44Z'
    case 'user':
      return 'M12 10a2.75 2.75 0 1 0 0-5.5A2.75 2.75 0 0 0 12 10Zm-4.5 7a4.5 4.5 0 0 1 9 0 .75.75 0 0 1-1.5 0 3 3 0 0 0-6 0 .75.75 0 0 1-1.5 0Z'
    case 'fork':
      return 'M9 4.5a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 1 1-3 0V6A1.5 1.5 0 0 1 9 4.5Zm6 0a1.5 1.5 0 0 1 1.5 1.5V9a1.5 1.5 0 1 1-3 0V6A1.5 1.5 0 0 1 15 4.5ZM8.25 11.25a.75.75 0 0 1 .75.75v6a.75.75 0 0 1-1.5 0v-6a.75.75 0 0 1 .75-.75Zm7.5 0a.75.75 0 0 1 .75.75v6a.75.75 0 0 1-1.5 0v-6a.75.75 0 0 1 .75-.75Z'
    case 'guest':
      return 'M12 4.75a2.75 2.75 0 1 1 0 5.5 2.75 2.75 0 0 1 0-5.5Zm-4.75 10a4.75 4.75 0 0 1 9.5 0 .75.75 0 0 1-1.5 0 3.25 3.25 0 0 0-6.5 0 .75.75 0 0 1-1.5 0Z'
    case 'group':
      return 'M7.75 6.5a2.25 2.25 0 1 1 0 4.5 2.25 2.25 0 0 1 0-4.5Zm8.5 0a2.25 2.25 0 1 1 0 4.5 2.25 2.25 0 0 1 0-4.5Zm-4.25 5.25a2.75 2.75 0 1 1 0-5.5 2.75 2.75 0 0 1 0 5.5Zm-6 5.5a.75.75 0 0 1-.75-.75 3.75 3.75 0 0 1 4.88-3.58 5.62 5.62 0 0 0-1.31 2.7.75.75 0 1 1-1.48-.24A2.25 2.25 0 0 0 6 16.5c0 .41-.34.75-.75.75Zm12 0a.75.75 0 0 1-.75-.75c0-.78-.18-1.51-.5-2.17a.75.75 0 1 1 1.35-.66c.43.87.65 1.83.65 2.83a.75.75 0 0 1-.75.75Zm-8.25-.75a3.25 3.25 0 0 1 6.5 0 .75.75 0 0 1-1.5 0 1.75 1.75 0 0 0-3.5 0 .75.75 0 0 1-1.5 0Z'
    case 'note':
      return 'M7.5 4.5A1.5 1.5 0 0 0 6 6v12a1.5 1.5 0 0 0 1.5 1.5h9A1.5 1.5 0 0 0 18 18V9.31a1.5 1.5 0 0 0-.44-1.06l-3.81-3.81A1.5 1.5 0 0 0 12.69 4.5H7.5Zm0 1.5h5V9a1 1 0 0 0 1 1h3v8a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V6a.5.5 0 0 1 .5-.5Zm6.5.56L16.94 9H13.5a.5.5 0 0 1-.5-.5V6.06Z'
    case 'order':
      return 'M7.5 5.25A2.25 2.25 0 0 1 9.75 3h4.5A2.25 2.25 0 0 1 16.5 5.25v13.5A2.25 2.25 0 0 1 14.25 21h-4.5A2.25 2.25 0 0 1 7.5 18.75V5.25Zm1.5 0v13.5c0 .41.34.75.75.75h4.5a.75.75 0 0 0 .75-.75V5.25a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0-.75.75Zm1.75 2.5a.75.75 0 0 1 .75-.75h1a.75.75 0 0 1 0 1.5h-1a.75.75 0 0 1-.75-.75Z'
    default:
      return ''
  }
}

function openTable(payload) {
  selectedTable.value = {
    ...payload.table,
    id: payload.table.apiTable?.id ?? payload.table.id,
    sectionTitle: payload.sectionTitle,
    apiTable: payload.table.apiTable ?? null,
  }
  view.value = 'order'
}

function goBackToTables() {
  view.value = 'tables'
}

function selectCategory(categoryId) {
  selectedCategoryId.value = categoryId
}

function addItem(item) {
  const existingItem = cart.find((cartItem) => cartItem.id === item.id)

  if (existingItem) {
    existingItem.quantity += 1
    return
  }

  cart.push({
    id: item.id,
    name: item.name,
    price: item.price,
    quantity: 1,
  })
}

function updateQuantity(payload) {
  const cartItem = cart.find((item) => item.id === payload.itemId)

  if (!cartItem) {
    return
  }

  cartItem.quantity += payload.delta

  if (cartItem.quantity <= 0) {
    const index = cart.findIndex((item) => item.id === payload.itemId)
    cart.splice(index, 1)
  }
}

function updateFlag(payload) {
  flags[payload.key] = payload.value
}

function formatCurrency(value) {
  return new Intl.NumberFormat('en-IN').format(value)
}
</script>

<template>
  <main class="petpooja-screen">
    <TopBar :top-menu="topMenu" :icon-path="iconPath" @new-order="goBackToTables" />

    <TableScreen
      v-if="view === 'tables'"
      :quick-actions="quickActions"
      :legend-items="legendItems"
      :sections="sections"
      :loading="tablesLoading"
      :error="tablesError"
      @open-table="openTable"
      @refresh="fetchTables"
      @quick-action="handleQuickAction"
      @edit-table="openEditTableModal"
      @delete-table="openDeleteConfirm"
    />

    <OrderScreen
      v-else
      :categories="categories"
      :selected-category-id="selectedCategoryId"
      :item-search="itemSearch"
      :short-code="shortCode"
      :filtered-items="filteredItems"
      :menu-items-loading="menuItemsLoading"
      :menu-items-error="menuItemsError"
      :order-types="orderTypes"
      :selected-order-type="selectedOrderType"
      :action-tabs="actionTabs"
      :selected-table="selectedTable"
      :cart="cart"
      :active-category="activeCategory"
      :flags="flags"
      :total-amount="totalAmount"
      :payment-modes="paymentModes"
      :selected-payment-mode="selectedPaymentMode"
      :icon-path="iconPath"
      :format-currency="formatCurrency"
      @select-category="selectCategory"
      @update:item-search="itemSearch = $event"
      @update:short-code="shortCode = $event"
      @add-item="addItem"
      @add-menu-item="openCreateMenuItemModal"
      @edit-menu-item="openEditMenuItemModal"
      @delete-menu-item="deleteMenuItem"
      @update:selected-order-type="selectedOrderType = $event"
      @go-back="goBackToTables"
      @update-quantity="updateQuantity"
      @toggle-flag="updateFlag"
      @update:selected-payment-mode="selectedPaymentMode = $event"
    />

    <div v-if="addTableModalOpen" class="dialog-backdrop" @click.self="closeAddTableModal">
      <form class="dialog-card" @submit.prevent="submitCreateTable">
        <h3>Add New Table</h3>

        <label class="dialog-field">
          <span>Table Name</span>
          <input v-model="newTableForm.name" type="text" placeholder="T-21" required />
        </label>

        <label class="dialog-field">
          <span>Floor</span>
          <select v-model="selectedFloor">
            <option value="" disabled>Select Floor</option>
            <option v-for="floor in uniqueFloors" :key="floor" :value="floor">{{ floor }}</option>
            <option value="other">Other (Enter new floor)</option>
          </select>
        </label>
        <label v-if="selectedFloor === 'other'" class="dialog-field">
          <span>New Floor</span>
          <input v-model="customFloor" type="text" placeholder="Enter new floor name" />
        </label>

        <label class="dialog-field">
          <span>Capacity</span>
          <input v-model.number="newTableForm.capacity" type="number" min="1" step="1" required />
        </label>

        <label class="dialog-field">
          <span>Status</span>
          <select v-model="newTableForm.status">
            <option value="blank">Blank</option>
            <option value="running">Running</option>
            <option value="printed">Printed</option>
            <option value="paid">Paid</option>
          </select>
        </label>

        <div class="dialog-actions">
          <button type="button" class="dialog-button dialog-button--ghost" @click="closeAddTableModal">Cancel</button>
          <button type="submit" class="dialog-button" :disabled="addTableSubmitting">{{ addTableSubmitting ? 'Saving...' : 'Create Table' }}</button>
        </div>
      </form>
    </div>

    <!-- Edit Table Modal -->
    <div v-if="editTableModalOpen" class="dialog-backdrop" @click.self="closeEditTableModal">
      <form class="dialog-card" @submit.prevent="submitEditTable">
        <h3>Edit Table</h3>

        <p v-if="editTableError" class="dialog-error">{{ editTableError }}</p>

        <label class="dialog-field">
          <span>Table Name</span>
          <input v-model="editTableForm.name" type="text" placeholder="T-21" required />
        </label>

        <label class="dialog-field">
          <span>Floor</span>
          <select v-model="editSelectedFloor">
            <option value="" disabled>Select Floor</option>
            <option v-for="floor in uniqueFloors" :key="floor" :value="floor">{{ floor }}</option>
            <option value="other">Other (Enter new floor)</option>
          </select>
        </label>
        <label v-if="editSelectedFloor === 'other'" class="dialog-field">
          <span>New Floor</span>
          <input v-model="editCustomFloor" type="text" placeholder="Enter new floor name" />
        </label>

        <label class="dialog-field">
          <span>Capacity</span>
          <input v-model.number="editTableForm.capacity" type="number" min="1" step="1" required />
        </label>

        <label class="dialog-field">
          <span>Status</span>
          <select v-model="editTableForm.status">
            <option value="blank">Blank</option>
            <option value="running">Running</option>
            <option value="printed">Printed</option>
            <option value="paid">Paid</option>
          </select>
        </label>

        <div class="dialog-actions">
          <button type="button" class="dialog-button dialog-button--ghost" @click="closeEditTableModal">Cancel</button>
          <button type="submit" class="dialog-button" :disabled="editTableSubmitting">{{ editTableSubmitting ? 'Saving...' : 'Save Changes' }}</button>
        </div>
      </form>
    </div>

    <!-- Delete Confirm Modal -->
    <div v-if="deleteConfirmOpen" class="dialog-backdrop" @click.self="closeDeleteConfirm">
      <div class="dialog-card">
        <h3>Delete Table</h3>
        <p class="dialog-confirm-text">Are you sure you want to delete table <strong>{{ deleteTargetTable?.name }}</strong>? This action cannot be undone.</p>
        <p v-if="deleteError" class="dialog-error">{{ deleteError }}</p>
        <div class="dialog-actions">
          <button type="button" class="dialog-button dialog-button--ghost" @click="closeDeleteConfirm">Cancel</button>
          <button type="button" class="dialog-button dialog-button--danger" :disabled="deleteSubmitting" @click="confirmDelete">{{ deleteSubmitting ? 'Deleting...' : 'Delete' }}</button>
        </div>
      </div>
    </div>

    <div v-if="menuItemModalOpen" class="dialog-backdrop" @click.self="closeMenuItemModal">
      <form class="dialog-card" @submit.prevent="submitMenuItem">
        <h3>{{ menuItemModalMode === 'edit' ? 'Edit Menu Item' : 'Add Menu Item' }}</h3>

        <p v-if="menuItemFormError" class="dialog-error">{{ menuItemFormError }}</p>

        <label class="dialog-field">
          <span>Item Name</span>
          <input v-model="menuItemForm.name" type="text" placeholder="Paneer Tikka" required />
        </label>

        <label class="dialog-field">
          <span>Category</span>
          <input v-model="menuItemForm.category" type="text" placeholder="Starters" required />
        </label>

        <label class="dialog-field">
          <span>Short Code</span>
          <input v-model="menuItemForm.short_code" type="text" placeholder="MI-101" />
        </label>

        <div class="dialog-grid-2">
          <label class="dialog-field">
            <span>Item Type</span>
            <select v-model="menuItemForm.item_type">
              <option value="Veg">Veg</option>
              <option value="Non-Veg">Non-Veg</option>
            </select>
          </label>

          <label class="dialog-field">
            <span>Price</span>
            <input v-model.number="menuItemForm.price" type="number" min="0" step="0.01" required />
          </label>
        </div>

        <label class="check-flag">
          <input v-model="menuItemForm.is_available" type="checkbox" />
          <span>Available</span>
        </label>

        <div class="dialog-actions">
          <button type="button" class="dialog-button dialog-button--ghost" @click="closeMenuItemModal">Cancel</button>
          <button type="submit" class="dialog-button" :disabled="menuItemSubmitting">{{ menuItemSubmitting ? 'Saving...' : menuItemModalMode === 'edit' ? 'Update Item' : 'Create Item' }}</button>
        </div>
      </form>
    </div>
  </main>
</template>
