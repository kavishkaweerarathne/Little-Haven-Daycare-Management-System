document.addEventListener('DOMContentLoaded', () => {
    fetchInventory();
    fetchStats();

    // Modal elements
    const modal = document.getElementById('inventoryModal');
    const addItemBtn = document.getElementById('addItemBtn');
    const closeBtn = document.querySelector('.close-modal');
    const inventoryForm = document.getElementById('inventoryForm');
    const modalTitle = document.getElementById('modalTitle');

    // Open modal for adding
    addItemBtn.addEventListener('click', () => {
        modalTitle.textContent = 'Add New Inventory Item';
        inventoryForm.reset();
        document.getElementById('itemId').value = '';
        modal.style.display = 'flex';
    });

    // Close modal
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Close modal on outside click
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Handle form submission
    inventoryForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(inventoryForm);
        const id = document.getElementById('itemId').value;
        const action = id ? 'update' : 'add';

        fetch(`inventory_actions.php?action=${action}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                modal.style.display = 'none';
                fetchInventory();
                fetchStats();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // Search functionality
    const searchInput = document.getElementById('inventorySearch');
    searchInput.addEventListener('input', () => {
        const term = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('#inventoryTable tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });
});

function fetchInventory() {
    fetch('inventory_actions.php?action=fetch_all')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderTable(data.data);
        }
    })
    .catch(error => console.error('Error:', error));
}

function fetchStats() {
    fetch('inventory_actions.php?action=stats')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalItemsCount').textContent = data.data.total_items;
            document.getElementById('lowStockCount').textContent = data.data.low_stock;
            // Assuming out_of_stock is returned or we use 0
            if(data.data.out_of_stock !== undefined) {
                document.getElementById('outOfStockCount').textContent = data.data.out_of_stock;
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function renderTable(items) {
    const tbody = document.querySelector('#inventoryTable tbody');
    tbody.innerHTML = '';

    if (items.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px;">No items found.</td></tr>';
        return;
    }

    items.forEach(item => {
        const isLowStock = parseInt(item.quantity) <= parseInt(item.reorder_threshold);
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.item_name}</td>
            <td><span class="badge badge-category">${item.category || 'N/A'}</span></td>
            <td>
                <span class="${isLowStock ? 'stock-low' : 'stock-ok'}">
                    ${item.quantity} ${item.unit}
                </span>
                ${isLowStock ? '<i class="fas fa-exclamation-triangle text-warning" title="Low Stock"></i>' : ''}
            </td>
            <td>${item.supplier_name || '-'}</td>
            <td>${item.expiry_date || 'No Expiry'}</td>
            <td>
                <div class="actions">
                    <button class="btn-icon edit-btn" onclick="editItem(${item.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn-icon delete-btn" onclick="deleteItem(${item.id})"><i class="fas fa-trash"></i></button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function editItem(id) {
    fetch(`inventory_actions.php?action=get_item&id=${id}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = data.data;
            document.getElementById('itemId').value = item.id;
            document.getElementById('item_name').value = item.item_name;
            document.getElementById('category').value = item.category;
            document.getElementById('quantity').value = item.quantity;
            document.getElementById('unit').value = item.unit;
            document.getElementById('reorder_threshold').value = item.reorder_threshold;
            document.getElementById('supplier_name').value = item.supplier_name;
            document.getElementById('supplier_contact').value = item.supplier_contact;
            document.getElementById('expiry_date').value = item.expiry_date;

            document.getElementById('modalTitle').textContent = 'Edit Inventory Item';
            document.getElementById('inventoryModal').style.display = 'flex';
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteItem(id) {
    if (confirm('Are you sure you want to delete this item? it will be moved to the archive.')) {
        const formData = new FormData();
        formData.append('id', id);

        fetch('inventory_actions.php?action=delete', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchInventory();
                fetchStats();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
