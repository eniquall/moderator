<?php

foreach($moderators as $moderator) {
	foreach($moderator->getAttributes() as $attributeName => $attributeValue) {
		$attributeValue = is_array($attributeValue) ? implode(', ', $attributeValue) : $attributeValue;
		echo '<b>' . $attributeName . "</b> " . $attributeValue . "<br>";
	}
	echo "<br><hr>";
}