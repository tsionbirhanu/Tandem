<?php
// app/Views/partials/request-actions.php
// Buttons for the actions available on a project request. Expects $req and $party ('client' | 'freelancer').
// Allowed actions come from ProjectRequest::TRANSITIONS so the UI and server rules stay in sync.

use App\Models\ProjectRequest;

$buttons = [
    'accept'   => ['Accept',        'btn-accent'],
    'start'    => ['Start work',    'btn-ink'],
    'complete' => ['Mark complete', 'btn-accent'],
    'reject'   => ['Decline',       'btn-ghost'],
    'cancel'   => ['Cancel',        'btn-ghost'],
];
$confirmText = [
    'reject'   => 'Decline this request?',
    'cancel'   => 'Cancel this request?',
    'complete' => 'Mark this project as complete? The client will be asked to review it.',
];
$available = array_keys(array_filter(
    ProjectRequest::TRANSITIONS[$req['status']] ?? [],
    fn($rule) => $rule[1] === $party
));
?>
<?php foreach ($buttons as $action => [$label, $class]): ?>
  <?php if (in_array($action, $available, true)): ?>
    <form action="/requests/<?= (int)$req['id'] ?>/status" method="POST" style="display: inline;"
          <?= isset($confirmText[$action]) ? '@submit="if (!confirm(' . e(json_encode($confirmText[$action])) . ')) $event.preventDefault()"' : '' ?>>
      <input type="hidden" name="action" value="<?= e($action) ?>">
      <button type="submit" class="btn btn-sm <?= $class ?>" <?= $class === 'btn-ghost' ? 'style="color: var(--danger);"' : '' ?>>
        <span class="spinner"></span><?= e($label) ?>
      </button>
    </form>
  <?php endif; ?>
<?php endforeach; ?>
