<?php
// service-edit.php
// Form and handler for updating existing service listings using PDO prepared statements.

require_once 'includes/Database.php';

$serviceId = (int)($_REQUEST['id'] ?? 0);
$errors = [];
$service = null;
$categories = [];
$freelancers = [];
$dbError = null;

try {
    $pdo = Database::getConnection();

    // Fetch existing service
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = :id");
    $stmt->execute([':id' => $serviceId]);
    $service = $stmt->fetch();

    if (!$service) {
        $dbError = "Service listing not found.";
    } else {
        // Fetch categories & freelancers for selects
        $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
        $freelancers = $pdo->query("SELECT id, name, email FROM users WHERE role = 'freelancer' ORDER BY name ASC")->fetchAll();
    }

} catch (Exception $e) {
    $dbError = "Database Connection Error: Unable to retrieve service data for editing.";
}

// Handle Update POST Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $service && !$dbError) {
    $title = trim($_POST['title'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $freelancer_id = (int)($_POST['freelancer_id'] ?? 0);
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validation
    if (empty($title)) {
        $errors['title'] = "Service title is required.";
    } elseif (mb_strlen($title) < 5) {
        $errors['title'] = "Service title must be at least 5 characters long.";
    }

    if (empty($category_id)) {
        $errors['category_id'] = "Please select a category.";
    }

    if (empty($freelancer_id)) {
        $errors['freelancer_id'] = "Please select a freelancer.";
    }

    if (empty($price) || !is_numeric($price) || (float)$price <= 0) {
        $errors['price'] = "Please enter a valid positive price.";
    }

    if (empty($description)) {
        $errors['description'] = "Service description is required.";
    } elseif (mb_strlen($description) < 20) {
        $errors['description'] = "Description must be at least 20 characters long.";
    }

    // Update database
    if (empty($errors)) {
        try {
            $updateStmt = $pdo->prepare("UPDATE services SET freelancer_id = :freelancer_id, category_id = :category_id, title = :title, description = :description, price = :price WHERE id = :id");
            $updateStmt->execute([
                ':freelancer_id' => $freelancer_id,
                ':category_id'   => $category_id,
                ':title'         => $title,
                ':description'   => $description,
                ':price'         => (float)$price,
                ':id'            => $serviceId,
            ]);

            header("Location: service-details.php?id={$serviceId}&msg=updated");
            exit;

        } catch (Exception $e) {
            $errors['global'] = "Failed to update service: " . $e->getMessage();
        }
    } else {
        // Keep user typed input in form
        $service['title'] = $title;
        $service['category_id'] = $category_id;
        $service['freelancer_id'] = $freelancer_id;
        $service['price'] = $price;
        $service['description'] = $description;
    }
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 650px; margin: 0 auto;">
        
        <div style="margin-bottom: var(--space-24);">
            <a href="service-details.php?id=<?php echo $serviceId; ?>" style="color: var(--color-text-muted); text-decoration: none;">&larr; Back to service details</a>
        </div>

        <div style="text-align: center; margin-bottom: var(--space-32);">
            <h1>Edit Service Listing</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Update service information, pricing, or description.</p>
        </div>

        <?php if ($dbError): ?>
            <div class="sg-card" style="text-align: center; padding: var(--space-48) var(--space-32);">
                <div class="alert alert-error">
                    <strong>Error:</strong> <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <a href="services.php" class="btn btn-secondary" style="margin-top: var(--space-16);">Back to Services</a>
            </div>
        <?php else: ?>

            <?php if (isset($errors['global'])): ?>
                <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                    <strong>Error:</strong> <?php echo htmlspecialchars($errors['global'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="sg-card">
                <form action="service-edit.php?id=<?php echo $serviceId; ?>" method="POST" novalidate>
                    <input type="hidden" name="id" value="<?php echo $serviceId; ?>">

                    <div class="form-group">
                        <label class="form-label" for="title">Service Title</label>
                        <input type="text" id="title" name="title" 
                               class="form-input <?php echo isset($errors['title']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (isset($errors['title'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-16);">
                        <div class="form-group">
                            <label class="form-label" for="category_id">Category</label>
                            <select id="category_id" name="category_id" class="form-select <?php echo isset($errors['category_id']) ? 'form-input-error' : ''; ?>">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($service['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['category_id'])): ?>
                                <div class="form-error-msg"><?php echo htmlspecialchars($errors['category_id'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="freelancer_id">Freelancer / Provider</label>
                            <select id="freelancer_id" name="freelancer_id" class="form-select <?php echo isset($errors['freelancer_id']) ? 'form-input-error' : ''; ?>">
                                <option value="">Select Freelancer</option>
                                <?php foreach ($freelancers as $free): ?>
                                    <option value="<?php echo (int)$free['id']; ?>" <?php echo ($service['freelancer_id'] == $free['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($free['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['freelancer_id'])): ?>
                                <div class="form-error-msg"><?php echo htmlspecialchars($errors['freelancer_id'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="price">Starting Price ($ USD)</label>
                        <input type="number" id="price" name="price" step="0.01" min="1" 
                               class="form-input <?php echo isset($errors['price']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($service['price'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (isset($errors['price'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['price'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Detailed Description</label>
                        <textarea id="message" name="description" rows="5" 
                                  class="form-input <?php echo isset($errors['description']) ? 'form-input-error' : ''; ?>"><?php echo htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: var(--space-16); margin-top: var(--space-24);">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
                        <a href="service-details.php?id=<?php echo $serviceId; ?>" class="btn btn-secondary">Cancel</a>
                    </div>
                    
                </form>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
