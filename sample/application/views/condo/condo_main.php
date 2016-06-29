
<div class="widget-box widget-color-blue">
	<div class="widget-header">
		<h5 class="widget-title smaller">Use the search box on the right to look for specific records</h5>
		<div class="widget-toolbar no-border">
			<button id="btn_add" class="btn btn-xs btn-primary" data-target="#Content_Modal" data-toggle="modal" data-backdrop="static" data-keyboard="false">
                            <i class="ace-icon fa fa-plus"></i>
                            Add
                        </button>
                    
		</div>
	</div>
    
        <div class="widget-body">
            <div class="widget-main">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="content" id="list_container">
                            <?= $recordList; ?>
                        </div>
                    </div>
                </div>
            </div>                
        </div>
    
</div>

<style type="text/css" class="init">
	
    #Content_Modal .modal-dialog {width:800px;}
    
</style>

<?= $modalContent; ?>



