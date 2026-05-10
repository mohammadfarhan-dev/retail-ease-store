<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

requireLogin();

if ($_SESSION['role'] === 'admin') {
    redirect('/retail-ease-store/admin/dashboard.php');
}

$question = "";
$response = "";
$error = "";
$success = "";

function getSmartAssistantResponse($question) {
    $q = strtolower($question);

    if (strpos($q, 'place order') !== false || strpos($q, 'make order') !== false || strpos($q, 'buy') !== false || strpos($q, 'purchase') !== false) {
        return "To place an order, go to the Product Catalogue, choose a product, click View & Order, enter the quantity, and submit the order form. Your order will first appear as pending until the admin reviews it.";
    }

    if (strpos($q, 'search') !== false || strpos($q, 'filter') !== false || strpos($q, 'find product') !== false) {
        return "You can search products from the Product Catalogue page by typing a product name or keyword. You can also filter products by category to narrow the results.";
    }

    if (strpos($q, 'order status') !== false || strpos($q, 'status') !== false || strpos($q, 'pending') !== false || strpos($q, 'approved') !== false || strpos($q, 'rejected') !== false || strpos($q, 'completed') !== false) {
        return "You can check your order status from the My Orders page. Pending means the admin has not reviewed it yet, approved means it has been accepted, rejected means it was declined, and completed means the order process is finished.";
    }

    if (strpos($q, 'cancel') !== false || strpos($q, 'remove order') !== false) {
        return "You can cancel an order only while it is still pending. Go to My Orders and click the Cancel button next to the pending order.";
    }

    if (strpos($q, 'account') !== false || strpos($q, 'register') !== false || strpos($q, 'login') !== false) {
        return "To use the system, create an account using the Register page, then log in with your email and password. After logging in, you can browse products, place orders, and view your order history.";
    }

    if (strpos($q, 'admin') !== false || strpos($q, 'product') !== false || strpos($q, 'manage') !== false) {
        return "Admin users can manage products, view customer orders, update order statuses, view users, and monitor system activity logs from the Admin Dashboard.";
    }

    if (strpos($q, 'smart assistant') !== false || strpos($q, 'ai') !== false || strpos($q, 'help') !== false) {
        return "The Smart Assistant provides basic guidance about how to use RetailEase Store. It uses local predefined responses and does not send your personal data to external AI services.";
    }

    return "I can help with common questions about RetailEase Store, such as how to place an order, search products, cancel pending orders, check order status, register/login, and understand admin features.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitizeInput($_POST['action'] ?? '');

    if ($action === 'ask') {
        $question = sanitizeInput($_POST['question'] ?? '');

        if (empty($question)) {
            $error = "Please enter a question.";
        } elseif (strlen($question) < 3) {
            $error = "Please enter a more detailed question.";
        } else {
            $response = getSmartAssistantResponse($question);
        }
    }

    if ($action === 'save_review') {
        $question = sanitizeInput($_POST['review_question'] ?? '');
        $response = sanitizeInput($_POST['review_response'] ?? '');
        $reviewed = isset($_POST['reviewed']) ? 1 : 0;

        if (empty($question) || empty($response)) {
            $error = "Unable to save review because the assistant response is missing.";
        } elseif (!$reviewed) {
            $error = "Please confirm that you reviewed the assistant response before saving.";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO ai_logs (user_id, question, assistant_response, reviewed_by_user)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $question,
                $response,
                $reviewed
            ]);

            logActivity(
                $pdo,
                $_SESSION['user_id'],
                'Smart Assistant Used',
                $_SESSION['name'] . ' reviewed and saved a Smart Assistant response.'
            );

            $success = "Assistant response reviewed and saved successfully.";
            $question = "";
            $response = "";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="page-header">
            <div>
                <h2>Smart Assistant</h2>
                <p>Ask simple questions about using RetailEase Store.</p>
            </div>
            <a href="dashboard.php" class="btn btn-light">Back to Dashboard</a>
        </div>

        <div class="ai-disclaimer">
            <h3>Responsible AI Notice</h3>
            <p>
                Smart Assistant responses are for guidance only and require human review.
                Do not enter sensitive personal information. This assistant uses local predefined responses and does not send data to external AI tools.
            </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="assistant-layout">
            <div class="assistant-card">
                <h3>Ask a Question</h3>

                <form method="POST" onsubmit="return validateAssistantForm();">
                    <input type="hidden" name="action" value="ask">

                    <div class="form-group">
                        <label>Your Question</label>
                        <textarea 
                            name="question" 
                            id="question" 
                            rows="5" 
                            placeholder="Example: How do I place an order?"><?php echo htmlspecialchars($question); ?></textarea>
                    </div>

                    <button type="submit" class="full-btn">Ask Smart Assistant</button>
                </form>
            </div>

            <div class="assistant-card">
                <h3>Suggested Questions</h3>

                <div class="suggested-questions">
                    <button type="button" onclick="setQuestion('How do I place an order?')">How do I place an order?</button>
                    <button type="button" onclick="setQuestion('How can I search products?')">How can I search products?</button>
                    <button type="button" onclick="setQuestion('How do I check my order status?')">How do I check my order status?</button>
                    <button type="button" onclick="setQuestion('Can I cancel my order?')">Can I cancel my order?</button>
                    <button type="button" onclick="setQuestion('What does pending order mean?')">What does pending order mean?</button>
                </div>
            </div>
        </div>

        <?php if (!empty($response)): ?>
            <div class="assistant-response">
                <h3>Assistant Response</h3>
                <p><?php echo htmlspecialchars($response); ?></p>

                <form method="POST" class="review-form">
                    <input type="hidden" name="action" value="save_review">
                    <input type="hidden" name="review_question" value="<?php echo htmlspecialchars($question); ?>">
                    <input type="hidden" name="review_response" value="<?php echo htmlspecialchars($response); ?>">

                    <label class="review-check">
                        <input type="checkbox" name="reviewed" checked>
                        I have reviewed this Smart Assistant response before saving it to the AI log.
                    </label>

                    <button type="submit" class="btn">Save Reviewed Response</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>
    </div>
</section>

<script>
function validateAssistantForm() {
    let question = document.getElementById("question").value.trim();

    if (question === "") {
        alert("Please enter a question.");
        return false;
    }

    if (question.length < 3) {
        alert("Please enter a more detailed question.");
        return false;
    }

    return true;
}

function setQuestion(text) {
    document.getElementById("question").value = text;
}
</script>

<?php include '../includes/footer.php'; ?>