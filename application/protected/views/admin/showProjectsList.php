<?php

foreach($projects as $project) {
	foreach($project->getAttributes() as $attributeName => $attributeValue) {
		echo '<b>' . $attributeName . "</b> " . $attributeValue . "<br>";
	}
	echo "<br><hr>";
}