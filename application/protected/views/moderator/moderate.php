<?php
echo __FILE__ . "<br>";

if (empty($content)) {
	echo "Sorry there are no content to moderate for you now.";
} else {
?>


<table>
<tr>
	<td>
		Content:

		<?php
		//foreach($content->data as list($contentItemType, $contentItemValue)) {
		foreach($content->data as $contentItem) {
			$contentItemValue = reset($contentItem);
			$contentItemType = key($contentItem);

			switch (mb_strtolower($contentItemType)) {
				case 'img':
					echo "<img src='" . $contentItemValue . "'></img>";
				break;

				case 'text':
				default:
					echo $contentItemValue;
				break;
			}
		}
		?>
	</td>
	<td>
		Moderation rule:

		<?php if (!empty($moderationRule)) {
			echo $moderationRule->text;
		} ?>
	</td>
</tr>
<tr>
	<td>
		Context:

		<?php
		foreach($content->context as $contentItem) {
			$contentItemValue = reset($contentItem);
			$contentItemType = key($contentItem);

			switch (mb_strtolower($contentItemType)) {
				case 'img':
					echo "<img src='" . $contentItemValue . "'></img>";
					break;

				case 'text':
				default:
					echo $contentItemValue;
					break;
			}
		}
		?>
	</td>
	<td>
		ProjectName:

		<?php
		echo $project->name;
		?>
	</td>
</tr>
<tr>
	<td><a href="#">Yes</a></td>
	<td><a href="#">No</a></td>
</tr>

</table>


<?php } ?>