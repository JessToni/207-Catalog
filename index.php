<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco-Catalog | Sustainability Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .eco-card { transition: transform 0.2s; }
        .eco-card:hover { transform: translateY(-5px); }
        .score-badge { font-size: 1.2rem; border-radius: 50px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-success mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">🌿 Eco-Catalog</span>
        <div class="d-flex">
            <a href="add_product_form.php" class="btn btn-outline-light btn-sm">Add Product</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row" id="product-list">
        <div class="text-center">
            <div class="spinner-border text-success" role="status"></div>
            <p>Loading sustainable products...</p>
        </div>
    </div>
</div>

<script>
    // Fetch data from your API
    async function loadProducts() {
        try {
            const response = await fetch('get_products.php');
            const products = await response.json();
            const container = document.getElementById('product-list');
            container.innerHTML = ''; // Clear the spinner

            if (products.length === 0) {
                container.innerHTML = '<p class="text-center">No products found. Be the first to add one!</p>';
                return;
            }

            products.forEach(product => {
                // Determine color based on score
                let badgeClass = 'bg-danger';
                if (product.total_score >= 85) badgeClass = 'bg-success';
                else if (product.total_score >= 60) badgeClass = 'bg-warning text-dark';

                container.innerHTML += `
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm eco-card">
                            <div class="card-body text-center">
                                <h5 class="card-title">${product.name}</h5>
                                <div class="badge ${badgeClass} score-badge mb-3">
                                    ${product.total_score}
                                </div>
                                <p class="text-muted small">
                                    Packaging: ${product.packaging_score} | 
                                    Sourcing: ${product.sourcing_score} | 
                                    Longevity: ${product.longevity_score}
                                </p>
                            </div>
                        </div>
                    </div>
                `;
            });
        } catch (error) {
            console.error('Error:', error);
        }
    }

    loadProducts();
</script>

</body>
</html>