<?php
// service-create.php
// Form and handler for creating a new service listing using Category, User, and Service models.

require_once 'includes/Database.php';

use App\Models\Category;
use App\Models\User;
use App\Models\Service;

$errors = [];
$title = '';
$category_id = '';
$freelancer_id = '';
$price = '';
$description = '';
$image_path = '';
$categories = [];
$freelancers = [];
$dbError = null;

try {
    $pdo = Database::getConnection();

    $categoryModel = new Category($pdo);
    $userModel     = new User($pdo);

    $categories  = $categoryModel->all();
    $freelancers = $userModel->findByRole('freelancer');

} catch (Exception $e) {
    $dbError = "Database Connection Error: Unable to load form requirements right now.";
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$dbError) {
    $title = trim($_POST['title'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $freelancer_id = (int)($_POST['freelancer_id'] ?? 0);
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_path = trim($_POST['image_path'] ?? '');

    // Validation
    if (empty($title)) {
        $errors['title'] = "Service title is required.";
    } elseif (mb_strlen($title) < 5) {
        $errors['title'] = "Service title must be at least 5 characters long.";
    }

    if (empty($category_id)) {
        $errors['category_id'] = "Please select a valid category.";
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

    // Insert into database via Service model
    if (empty($errors)) {
        try {
            $serviceModel = new Service($pdo);

            $newServiceId = $serviceModel->create([
                'freelancer_id' => $freelancer_id,
                'category_id'   => $category_id,
                'title'         => $title,
                'description'   => $description,
                'price'         => (float)$price,
            ]);

            if (!empty($image_path)) {
                $serviceModel->addImage($newServiceId, $image_path);
            }

            header("Location: service-details.php?id={$newServiceId}&msg=created");
            exit;

        } catch (Exception $e) {
            $errors['global'] = "Failed to create service: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 650px; margin: 0 auto;">
        
        <div style="margin-bottom: var(--space-24);">
            <a href="services.php" style="color: var(--color-text-muted); text-decoration: none;">&larr; Back to services</a>
        </div>

        <div style="text-align: center; margin-bottom: var(--space-32);">
            <h1>Create New Service</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Add a new service listing to the Tandem marketplace network.</p>
        </div>

        <?php if ($dbError): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php else: ?>

            <?php if (isset($errors['global'])): ?>
                <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                    <strong>Error:</strong> <?php echo htmlspecialchars($errors['global'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="sg-card">
                <form action="service-create.php" method="POST" novalidate>
                    
                    <div class="form-group">
                        <label class="form-label" for="title">Service Title</label>
                        <input type="text" id="title" name="title" 
                               class="form-input <?php echo isset($errors['title']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" 
                               placeholder="e.g. Custom React Web Application Development">
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
                                    <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
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
                                    <option value="<?php echo (int)$free['id']; ?>" <?php echo ($freelancer_id == $free['id']) ? 'selected' : ''; ?>>
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
                               value="<?php echo htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>" 
                               placeholder="e.g. 1500.00">
                        <?php if (isset($errors['price'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['price'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Detailed Description</label>
                        <textarea id="message" name="description" rows="5" 
                                  class="form-input <?php echo isset($errors['description']) ? 'form-input-error' : ''; ?>"
                                  placeholder="Describe scope, deliverables, timeline, and requirements (min 20 characters)..."><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="image_path">Cover Image Path / URL (Optional)</label>
                        <input type="text" id="image_path" name="image_path" 
                               class="form-input" 
                               value="<?php echo htmlspecialchars($image_path, ENT_QUOTES, 'UTF-8'); ?>" 
                               placeholder="e.g. assets/images/services/react-hero.jpg">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: var(--space-8);">Publish Service</button>
                    
                </form>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
