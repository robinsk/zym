<!-- Simple -->
Last updated <?= $this->timeSince($timestamp); ?> ago

<!-- Time since a specified timestamp -->
<?= $this->timeSince($timestamp, strtotime('+1 day')); ?>