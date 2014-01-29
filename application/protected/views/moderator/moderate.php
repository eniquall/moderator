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


<script>
	$(document).ready(function(){
		$('.approveButton').on('click', function () {
			var approveResult = $(this).attr('data-approveResult');
			$('#moderateForm_approveResult').val(approveResult);
			$('#moderateForm').submit();
		});
	});

</script>


<form name="moderateForm" id="moderateForm" method="POST">
	<?php echo CHtml::hiddenField('moderateForm[contentId]', $content->getId()); ?>
	<?php echo CHtml::hiddenField('moderateForm[approveResult]', ''); ?>
</form>

<table class="moderationTable" cellspacing="2" border="1" cellpadding="5">
<tr>
	<td class="content">
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
					echo "<div><b>text:</b> " . $contentItemValue . "</div>";
				break;
			}
			echo "</div>";
		}
		?>
	</td>
	<td class="moderationRule">
		<?php if (!empty($moderationRule)) {
			echo $moderationRule->text;
		} ?>
	</td>
</tr>
<tr>
	<td class="context">
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
					echo "<div><b>text:</b> " . $contentItemValue . "</div>";
				break;
			}
		}
		?>
	</td>
	<td class="project">
		<?php
			echo $project->name;
		?>
	</td>
</tr>
<tr>
	<td class="approve">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Approve',
			'type'=>'success',
			'size'=>'large',
			'htmlOptions' => array(
				'class' => 'approveButton',
				'data-approveResult' => '1' //disapprove
			),
		)); ?>
	</td>
	<td class="disapprove">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'label'=>'Disapprove',
			'type'=>'danger',
			'size'=>'large',
			'htmlOptions' => array(
				'class' => 'approveButton',
				'data-approveResult' => '0' //disapprove
			),
		)); ?>
	</td>
</tr>

</table>

<?php } ?>