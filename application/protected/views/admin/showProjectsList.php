<?php

foreach($projects as $project) {
	foreach($project->getAttributes() as $attributeName => $attributeValue) {

		switch ($attributeName) {
			case 'password':
				continue 2;
				break;

			default:
		}

		$attributeValue = is_array($attributeValue) ? implode(', ', $attributeValue) : $attributeValue;
		echo '<b>' . $attributeName . "</b> " . $attributeValue . "<br>";
	}

	echo "<a href='" . $this->createUrl('/project/editProfile', array('id' => $project->getId())) . "'>edit</a>";
	echo "<br><hr>";
}