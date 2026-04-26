<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco-Catalog | Sustainability Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .eco-card { transition: transform 0.2s, box-shadow 0.2s; border: none; }
        .eco-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .score-badge { font-size: 1.2rem; border-radius: 50px; min-width: 60px; }
        .navbar-brand { font-weight: 700; letter-spacing: -0.5px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-success shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">🌿 Eco-Catalog</span>
        <div class="d-flex">
            <button class="btn btn-outline-light btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addProductModal">
                + Add Product
            </button>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row" id="product-list">
        <div class="text-center py-5">
            <div class="spinner-border text-success" role="status"></div>
            <p class="mt-2 text-muted">Loading sustainable products...</p>
        </div>
    </div>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Sustainable Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" id="name" class="form-control" placeholder="e.g. Bamboo Toothbrush" required>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label class="form-label text-truncate">Packaging</label>
                            <input type="number" id="pkg" class="form-control" min="0" max="100" value="0">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label text-truncate">Sourcing</label>
                            <input type="number" id="src" class="form-control" min="0" max="100" value="0">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label text-truncate">Longevity</label>
                            <input type="number" id="lng" class="form-control" min="0" max="100" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    async function checkAuth() {
        const response = await fetch('check_session.php');
        const status = await response.json();
        
        const authLinks = document.querySelector('.d-flex'); // The container for our button
        if (!status.loggedIn) {
            authLinks.innerHTML = `<a href="auth.php" class="btn btn-outline-light btn-sm">Login to Add</a>`;
        }
    }
    // XSS Protection Helper
    function escapeHTML(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Fetch and Load Products
    async function loadProducts() {
        const container = document.getElementById('product-list');
        try {
            const response = await fetch('get_products.php');
            const products = await response.json();
            container.innerHTML = ''; 

            if (products.length === 0) {
                container.innerHTML = '<div class="col-12 text-center text-muted py-5"><h5>No products found yet.</h5></div>';
                return;
            }

            products.forEach(product => {
                let badgeClass = 'bg-danger';
                if (product.total_score >= 85) badgeClass = 'bg-success';
                else if (product.total_score >= 60) badgeClass = 'bg-warning text-dark';

                // Use escapeHTML for the product name to prevent XSS
                container.innerHTML += `
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm eco-card">
                            <div class="card-body text-center">
                                <h5 class="card-title fw-bold">${escapeHTML(product.name)}</h5>
                                <div class="badge ${badgeClass} score-badge mb-3">
                                    ${parseFloat(product.total_score).toFixed(1)}
                                </div>
                                <div class="p-2 bg-light rounded-pill mb-2">
                                    <small class="text-uppercase fw-bold text-muted" style="font-size: 0.65rem;">
                                        Pkg: ${product.packaging_score} | Src: ${product.sourcing_score} | Lng: ${product.longevity_score}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } catch (error) {
            container.innerHTML = `<div class="alert alert-danger">Error loading data. Check if get_products.php is active.</div>`;
            console.error('Error:', error);
        }
    }

    // Handle Form Submission via AJAX
    document.getElementById('productForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('name', document.getElementById('name').value);
        formData.append('packaging', document.getElementById('pkg').value);
        formData.append('sourcing', document.getElementById('src').value);
        formData.append('longevity', document.getElementById('lng').value);

        try {
            const response = await fetch('add_product.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (response.ok) {
                // Close Modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                modal.hide();
                // Reset form and reload grid
                document.getElementById('productForm').reset();
                loadProducts();
            } else {
                alert(result.error || "Failed to save product.");
            }
        } catch (err) {
            alert("Connection error. Is the server running?");
        }
    });

    // Initial load
    loadProducts();
</script>

</body>
</html>