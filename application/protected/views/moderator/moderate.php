<?php

if (empty($content)) {
	echo "Sorry there are no content to moderate for you now.";
} else {
?>

<style>
	table.moderationTable, td, tr {
		border: 1px solid #000000;
		text-align: center;
	}
</style>

<table class="moderationTable" cellspacing="2" border="1" cellpadding="5">
<tr>
	<td class="content">
		Content:

		<?php
		foreach($content->data as $contentItem) {
			echo "<div>";
			$contentItemValue = reset($contentItem);
			$contentItemType = key($contentItem);

			switch (mb_strtolower($contentItemType)) {
				case 'img':
					echo "<img src='" . $contentItemValue . "'><br>";
				break;

				case 'text':
				default:
					echo $contentItemValue;
				break;
			}
			echo "</div>";
		}
		?>
	</td>
	<td class="moderationRule">
		Moderation rule:

		<?php if (!empty($moderationRule)) {
			echo $moderationRule->text;
		} ?>
	</td>
</tr>
<tr>
	<td class="context">
		Context:
		<?php
		foreach($content->context as $contentItem) {
			$contentItemValue = reset($contentItem);
			$contentItemType = key($contentItem);

			switch (mb_strtolower($contentItemType)) {
				case 'img':
					echo "<img src='" . $contentItemValue . "'><br>";
					break;

				case 'text':
				default:
					echo $contentItemValue;
					break;
			}
		}
		?>
	</td>
	<td class="project">
		ProjectName:

		<?php
		echo $project->name;
		?>
	</td>
</tr>
<tr>
	<td class="approve"><?php $this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Approve',
			'type'=>'success',
			'size'=>'large',
		)); ?>
	</td>
	<td class="disapprove">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Disapprove',
			'type'=>'danger',
			'size'=>'large',
		)); ?>
	</td>
</tr>

</table>


<?php } ?>