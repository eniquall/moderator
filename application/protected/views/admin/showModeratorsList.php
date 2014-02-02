<?php

foreach($moderators as $moderator) {
	foreach($moderator->getAttributes() as $attributeName => $attributeValue) {

		switch ($attributeName) {
			case 'langs':
				$attrs = [];
				foreach($attributeValue as $langId) {
					$attrs[] = LanguagesHelper::getLanguageNameById($langId);
				}
				$attributeValue = $attrs;
			break;

			case 'password':
				continue;
			break;

			case 'projects':
				if (!empty($attributeValue)) {
					$attrs = [];

					arsort($attributeValue);
					foreach((array)$attributeValue as $projectId => $votes) {
						$project = ProjectModel::model()->findByPk(new MongoId($projectId));
						$attrs[] = $project->name . ' (' . $votes . ') ';
					}
					$attributeValue = $attrs;
				}
			break;

			case 'createDate':
			case 'lastActivity':
				$attributeValue = !empty($attributeValue) ? date("Y-m-d H:i:s", $attributeValue) : '';
			break;

			default:
		}



		$attributeValue = is_array($attributeValue) ? implode(', ', $attributeValue) : $attributeValue;
		echo '<b>' . $attributeName . "</b> " . $attributeValue . "<br>";
	}



	echo "<a href='" . $this->createUrl('/moderator/editProfile', array('id' => $moderator->getId())) . "'>edit</a>";
	echo "<br><hr>";
}