<?php
$this->breadcrumbs=array(
	'Stories',
);

$this->menu=array(
	array('label'=>'Create Story', 'url'=>array('create')),
);
?>

<h1>Stories</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
