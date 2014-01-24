<?php

foreach($projects as $project) {
	foreach($project->getAttributes() as $attributeName => $attributeValue) {
		echo '<b>' . $attributeName . "</b> " . $attributeValue . "<br>";
	}

	echo "<a href='" . $this->createUrl('/project/editProfile', array('id' => $project->getId())) . "'>edit</a>";
	echo "<br><hr>";
}