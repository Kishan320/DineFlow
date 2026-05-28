// Mock data — replace with API calls to Laravel backend

export const menuCategories = [
  { id: 'cat-all', label: 'All', icon: '🍽️' },
  { id: 'cat-starters', label: 'Starters', icon: '🥗' },
  { id: 'cat-mains', label: 'Mains', icon: '🍝' },
  { id: 'cat-pizza', label: 'Pizza', icon: '🍕' },
  { id: 'cat-burgers', label: 'Burgers', icon: '🍔' },
  { id: 'cat-desserts', label: 'Desserts', icon: '🍰' },
  { id: 'cat-drinks', label: 'Drinks', icon: '🥤' },
];

export const menuItems = [
  { id: 'item-001', name: 'Bruschetta al Pomodoro', category: 'Starters', price: 9.50, tax: 8, description: 'Grilled bread with fresh tomatoes and basil', available: true, image: '/images/menu/item-001.jpg', imageAlt: 'Bruschetta topped with diced tomatoes and fresh basil on toasted bread', popular: true },
  { id: 'item-002', name: 'Calamari Fritti', category: 'Starters', price: 13.00, tax: 8, description: 'Crispy fried squid rings with marinara', available: true, image: '/images/menu/item-002.jpg', imageAlt: 'Golden crispy calamari rings served with red marinara dipping sauce' },
  { id: 'item-003', name: 'Spaghetti Carbonara', category: 'Mains', price: 18.50, tax: 8, description: 'Classic carbonara with guanciale and egg', available: true, image: '/images/menu/item-003.jpg', imageAlt: 'Creamy spaghetti carbonara with crispy pancetta and black pepper', popular: true },
  { id: 'item-004', name: 'Penne Arrabbiata', category: 'Mains', price: 16.00, tax: 8, description: 'Spicy tomato sauce with garlic and chili', available: true, image: '/images/menu/item-004.jpg', imageAlt: 'Penne pasta in spicy red arrabbiata sauce garnished with fresh parsley' },
  { id: 'item-005', name: 'Margherita Pizza', category: 'Pizza', price: 15.00, tax: 8, description: 'Classic tomato, mozzarella and basil', available: true, image: '/images/menu/item-005.jpg', imageAlt: 'Classic margherita pizza with fresh mozzarella tomato sauce and basil leaves', popular: true },
  { id: 'item-006', name: 'Quattro Stagioni', category: 'Pizza', price: 18.00, tax: 8, description: 'Four seasons pizza with seasonal toppings', available: true, image: '/images/menu/item-006.jpg', imageAlt: 'Four seasons pizza divided into quadrants with mushrooms ham artichokes and olives' },
  { id: 'item-007', name: 'Classic Beef Burger', category: 'Burgers', price: 16.50, tax: 8, description: 'Angus beef patty with lettuce, tomato, cheese', available: true, image: '/images/menu/item-007.jpg', imageAlt: 'Juicy beef burger with melted cheddar lettuce tomato and brioche bun' },
  { id: 'item-008', name: 'Truffle Burger', category: 'Burgers', price: 22.00, tax: 8, description: 'Wagyu beef with truffle mayo and arugula', available: false, image: '/images/menu/item-008.jpg', imageAlt: 'Premium wagyu burger with truffle aioli arugula and caramelized onions' },
  { id: 'item-009', name: 'Tiramisu', category: 'Desserts', price: 9.00, tax: 5, description: 'Classic Italian tiramisu with espresso', available: true, image: '/images/menu/item-009.jpg', imageAlt: 'Layered tiramisu dessert dusted with cocoa powder in a glass dish', popular: true },
  { id: 'item-010', name: 'Panna Cotta', category: 'Desserts', price: 8.00, tax: 5, description: 'Vanilla panna cotta with berry compote', available: true, image: '/images/menu/item-010.jpg', imageAlt: 'Smooth vanilla panna cotta topped with fresh mixed berry compote' },
  { id: 'item-011', name: 'San Pellegrino', category: 'Drinks', price: 4.50, tax: 5, description: 'Sparkling mineral water 750ml', available: true, image: '/images/menu/item-011.jpg', imageAlt: 'Glass bottle of San Pellegrino sparkling mineral water with condensation' },
  { id: 'item-012', name: 'House Red Wine', category: 'Drinks', price: 8.00, tax: 5, description: 'Chianti Classico, glass 150ml', available: true, image: '/images/menu/item-012.jpg', imageAlt: 'Glass of deep red Chianti wine against blurred restaurant background' },
  { id: 'item-013', name: 'Risotto ai Funghi', category: 'Mains', price: 19.50, tax: 8, description: 'Creamy mushroom risotto with parmesan', available: true, image: '/images/menu/item-013.jpg', imageAlt: 'Creamy mushroom risotto with shaved parmesan and fresh thyme garnish' },
  { id: 'item-014', name: 'Caprese Salad', category: 'Starters', price: 11.00, tax: 8, description: 'Buffalo mozzarella, tomatoes, basil, EVOO', available: true, image: '/images/menu/item-014.jpg', imageAlt: 'Fresh caprese salad with buffalo mozzarella sliced tomatoes and basil drizzled with olive oil' },
];

export const tables = [
  { id: 'tbl-01', number: 1, capacity: 2, status: 'Available' },
  { id: 'tbl-02', number: 2, capacity: 4, status: 'Occupied', waiter: 'Luca Moretti', order: 'ORD-2847' },
  { id: 'tbl-03', number: 3, capacity: 4, status: 'Occupied', waiter: 'Sofia Ricci', order: 'ORD-2849' },
  { id: 'tbl-04', number: 4, capacity: 6, status: 'Reserved' },
  { id: 'tbl-05', number: 5, capacity: 2, status: 'Available' },
  { id: 'tbl-06', number: 6, capacity: 8, status: 'Occupied', waiter: 'Luca Moretti', order: 'ORD-2851' },
  { id: 'tbl-07', number: 7, capacity: 4, status: 'Available' },
  { id: 'tbl-08', number: 8, capacity: 6, status: 'Occupied', waiter: 'Elena Bianchi', order: 'ORD-2853' },
  { id: 'tbl-09', number: 9, capacity: 2, status: 'Reserved' },
  { id: 'tbl-10', number: 10, capacity: 4, status: 'Available' },
];

export const customers = [
  { id: 'cust-001', name: 'Isabella Romano', phone: '+1 555-0101', email: 'isabella@email.com', visits: 24, totalSpent: 847.50 },
  { id: 'cust-002', name: 'Marco Ferretti', phone: '+1 555-0102', email: 'marco.f@email.com', visits: 11, totalSpent: 412.00 },
  { id: 'cust-003', name: 'Giulia Conti', phone: '+1 555-0103', email: 'giulia.c@email.com', visits: 6, totalSpent: 198.75 },
  { id: 'cust-004', name: 'Alessandro Gallo', phone: '+1 555-0104', email: 'alex.gallo@email.com', visits: 32, totalSpent: 1240.00 },
  { id: 'cust-005', name: 'Chiara Martinelli', phone: '+1 555-0105', email: 'chiara.m@email.com', visits: 3, totalSpent: 89.50 },
  { id: 'cust-006', name: 'Walk-In Guest', phone: '', email: '', visits: 0, totalSpent: 0 },
];

export const waiters = [
  { id: 'wait-01', name: 'Luca Moretti', phone: '+1 555-0201', status: 'Busy', tablesAssigned: 2 },
  { id: 'wait-02', name: 'Sofia Ricci', phone: '+1 555-0202', status: 'Busy', tablesAssigned: 1 },
  { id: 'wait-03', name: 'Elena Bianchi', phone: '+1 555-0203', status: 'Busy', tablesAssigned: 1 },
  { id: 'wait-04', name: 'Matteo Russo', phone: '+1 555-0204', status: 'Available', tablesAssigned: 0 },
  { id: 'wait-05', name: 'Valentina Costa', phone: '+1 555-0205', status: 'Off Duty', tablesAssigned: 0 },
];

export const recentOrders = [
  { id: 'ord-2847', billNo: 'BILL-2847', salesCode: 'SC-2847', customer: 'Isabella Romano', table: 'Table 2', waiter: 'Luca Moretti', items: [{ id: 'oi-1', name: 'Spaghetti Carbonara', category: 'Mains', price: 18.50, quantity: 2, total: 37.00 }, { id: 'oi-2', name: 'Margherita Pizza', category: 'Pizza', price: 15.00, quantity: 1, total: 15.00 }, { id: 'oi-3', name: 'House Red Wine', category: 'Drinks', price: 8.00, quantity: 2, total: 16.00 }], billType: 'Dine In', subtotal: 68.00, tax: 5.44, discount: 0, total: 73.44, paid: 73.44, balance: 0, status: 'Paid', date: '05/27/2026', time: '13:42' },
  { id: 'ord-2849', billNo: 'BILL-2849', salesCode: 'SC-2849', customer: 'Marco Ferretti', table: 'Table 3', waiter: 'Sofia Ricci', items: [{ id: 'oi-4', name: 'Quattro Stagioni', category: 'Pizza', price: 18.00, quantity: 1, total: 18.00 }, { id: 'oi-5', name: 'Calamari Fritti', category: 'Starters', price: 13.00, quantity: 1, total: 13.00 }], billType: 'Dine In', subtotal: 31.00, tax: 2.48, discount: 3.10, total: 30.38, paid: 0, balance: 30.38, status: 'Pending', date: '05/27/2026', time: '13:55' },
  { id: 'ord-2851', billNo: 'BILL-2851', salesCode: 'SC-2851', customer: 'Walk-In Guest', table: 'Table 6', waiter: 'Luca Moretti', items: [{ id: 'oi-6', name: 'Classic Beef Burger', category: 'Burgers', price: 16.50, quantity: 3, total: 49.50 }, { id: 'oi-7', name: 'San Pellegrino', category: 'Drinks', price: 4.50, quantity: 3, total: 13.50 }], billType: 'Dine In', subtotal: 63.00, tax: 5.04, discount: 0, total: 68.04, paid: 68.04, balance: 0, status: 'Paid', date: '05/27/2026', time: '14:02' },
  { id: 'ord-2853', billNo: 'BILL-2853', salesCode: 'SC-2853', customer: 'Giulia Conti', table: 'Table 8', waiter: 'Elena Bianchi', items: [{ id: 'oi-8', name: 'Risotto ai Funghi', category: 'Mains', price: 19.50, quantity: 1, total: 19.50 }, { id: 'oi-9', name: 'Tiramisu', category: 'Desserts', price: 9.00, quantity: 1, total: 9.00 }], billType: 'Dine In', subtotal: 28.50, tax: 1.43, discount: 0, total: 29.93, paid: 0, balance: 29.93, status: 'Pending', date: '05/27/2026', time: '14:07' },
  { id: 'ord-2845', billNo: 'BILL-2845', salesCode: 'SC-2845', customer: 'Alessandro Gallo', table: 'Takeaway', waiter: 'Matteo Russo', items: [{ id: 'oi-10', name: 'Margherita Pizza', category: 'Pizza', price: 15.00, quantity: 2, total: 30.00 }], billType: 'Takeaway', subtotal: 30.00, tax: 2.40, discount: 5.00, total: 27.40, paid: 27.40, balance: 0, status: 'Paid', date: '05/27/2026', time: '13:15' },
  { id: 'ord-2843', billNo: 'BILL-2843', salesCode: 'SC-2843', customer: 'Chiara Martinelli', table: 'Takeaway', waiter: 'Sofia Ricci', items: [{ id: 'oi-11', name: 'Penne Arrabbiata', category: 'Mains', price: 16.00, quantity: 1, total: 16.00 }], billType: 'Takeaway', subtotal: 16.00, tax: 1.28, discount: 0, total: 17.28, paid: 17.28, balance: 0, status: 'Paid', date: '05/27/2026', time: '12:48' },
  { id: 'ord-2840', billNo: 'BILL-2840', salesCode: 'SC-2840', customer: 'Walk-In Guest', table: 'Table 1', waiter: 'Luca Moretti', items: [{ id: 'oi-12', name: 'Bruschetta al Pomodoro', category: 'Starters', price: 9.50, quantity: 2, total: 19.00 }], billType: 'Dine In', subtotal: 19.00, tax: 1.52, discount: 0, total: 20.52, paid: 0, balance: 20.52, status: 'Cancelled', date: '05/27/2026', time: '12:20' },
  { id: 'ord-2838', billNo: 'BILL-2838', salesCode: 'SC-2838', customer: 'Isabella Romano', table: 'Table 4', waiter: 'Elena Bianchi', items: [{ id: 'oi-13', name: 'Caprese Salad', category: 'Starters', price: 11.00, quantity: 1, total: 11.00 }, { id: 'oi-14', name: 'Spaghetti Carbonara', category: 'Mains', price: 18.50, quantity: 2, total: 37.00 }, { id: 'oi-15', name: 'Tiramisu', category: 'Desserts', price: 9.00, quantity: 2, total: 18.00 }, { id: 'oi-16', name: 'House Red Wine', category: 'Drinks', price: 8.00, quantity: 1, total: 8.00 }], billType: 'Dine In', subtotal: 74.00, tax: 5.92, discount: 7.40, total: 72.52, paid: 72.52, balance: 0, status: 'Paid', date: '05/27/2026', time: '11:55' },
  { id: 'ord-2835', billNo: 'BILL-2835', salesCode: 'SC-2835', customer: 'Marco Ferretti', table: 'Table 5', waiter: 'Matteo Russo', items: [{ id: 'oi-17', name: 'Classic Beef Burger', category: 'Burgers', price: 16.50, quantity: 2, total: 33.00 }, { id: 'oi-18', name: 'San Pellegrino', category: 'Drinks', price: 4.50, quantity: 2, total: 9.00 }], billType: 'Dine In', subtotal: 42.00, tax: 3.36, discount: 0, total: 45.36, paid: 45.36, balance: 0, status: 'Paid', date: '05/27/2026', time: '11:30' },
  { id: 'ord-2830', billNo: 'BILL-2830', salesCode: 'SC-2830', customer: 'Alessandro Gallo', table: 'Table 7', waiter: 'Sofia Ricci', items: [{ id: 'oi-19', name: 'Risotto ai Funghi', category: 'Mains', price: 19.50, quantity: 2, total: 39.00 }, { id: 'oi-20', name: 'Panna Cotta', category: 'Desserts', price: 8.00, quantity: 2, total: 16.00 }], billType: 'Dine In', subtotal: 55.00, tax: 3.85, discount: 0, total: 58.85, paid: 58.85, balance: 0, status: 'Paid', date: '05/27/2026', time: '10:55' },
  { id: 'ord-2828', billNo: 'BILL-2828', salesCode: 'SC-2828', customer: 'Giulia Conti', table: 'Delivery', waiter: 'Matteo Russo', items: [{ id: 'oi-21', name: 'Margherita Pizza', category: 'Pizza', price: 15.00, quantity: 1, total: 15.00 }, { id: 'oi-22', name: 'Tiramisu', category: 'Desserts', price: 9.00, quantity: 1, total: 9.00 }], billType: 'Delivery', subtotal: 24.00, tax: 1.44, discount: 0, total: 25.44, paid: 25.44, balance: 0, status: 'Paid', date: '05/27/2026', time: '10:20' },
  { id: 'ord-2825', billNo: 'BILL-2825', salesCode: 'SC-2825', customer: 'Chiara Martinelli', table: 'Table 9', waiter: 'Elena Bianchi', items: [{ id: 'oi-23', name: 'Quattro Stagioni', category: 'Pizza', price: 18.00, quantity: 2, total: 36.00 }], billType: 'Dine In', subtotal: 36.00, tax: 2.88, discount: 0, total: 38.88, paid: 0, balance: 38.88, status: 'Draft', date: '05/27/2026', time: '09:45' },
];

export const hourlySalesData = [
  { hour: '9AM', orders: 4, revenue: 112 },
  { hour: '10AM', orders: 7, revenue: 218 },
  { hour: '11AM', orders: 12, revenue: 387 },
  { hour: '12PM', orders: 24, revenue: 748 },
  { hour: '1PM', orders: 31, revenue: 964 },
  { hour: '2PM', orders: 18, revenue: 521 },
  { hour: '3PM', orders: 9, revenue: 274 },
  { hour: '4PM', orders: 6, revenue: 183 },
  { hour: '5PM', orders: 14, revenue: 432 },
  { hour: '6PM', orders: 28, revenue: 887 },
  { hour: '7PM', orders: 35, revenue: 1124 },
  { hour: '8PM', orders: 22, revenue: 698 },
];

export const weeklySalesData = [
  { day: 'Mon', revenue: 2840, orders: 67 },
  { day: 'Tue', revenue: 3120, orders: 74 },
  { day: 'Wed', revenue: 2680, orders: 61 },
  { day: 'Thu', revenue: 3540, orders: 84 },
  { day: 'Fri', revenue: 4820, orders: 112 },
  { day: 'Sat', revenue: 5640, orders: 134 },
  { day: 'Sun', revenue: 4210, orders: 98 },
];

export const topSellingItems = [
  { id: 'tsi-1', name: 'Spaghetti Carbonara', category: 'Mains', quantity: 47, revenue: 869.50 },
  { id: 'tsi-2', name: 'Margherita Pizza', category: 'Pizza', quantity: 42, revenue: 630.00 },
  { id: 'tsi-3', name: 'Classic Beef Burger', category: 'Burgers', quantity: 38, revenue: 627.00 },
  { id: 'tsi-4', name: 'Tiramisu', category: 'Desserts', quantity: 35, revenue: 315.00 },
  { id: 'tsi-5', name: 'Bruschetta al Pomodoro', category: 'Starters', quantity: 31, revenue: 294.50 },
];
