<h1>Moderation rules for project <?php echo $project->name; ?>: </h1>

<?php
foreach ((array)$rules as $rule) {
	echo "Type of content: " . ContentHelper::getTypeNameByType($rule->type) . "<br>";
	echo "Text: " . $rule->text . "<br>";
	echo "Level: " . $rule->level . "<br>";

	echo "<a href=\"" . $this->createUrl('/project/editModerationRule', array('id' => (string) $rule->_id)) . "\">Edit rule</a><br><br>";

	echo "<hr>";
}