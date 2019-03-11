<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel with-nav-tabs panel-info">
                    <div class="panel-heading panel-heading-x">
                        <ul class="nav nav-tabs">
                            <li class="active" style="font-weight:bold">
                                <a href="#tab1default" data-toggle="tab">Uploading</a>
                            </li>
                        </ul>
                    </div>
                    <div class="panel-body">
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="tab1default"> 
                                <div class="form-container">  
                                    <div class="col-sm-12">
                                    <label for=""></label>
                                        <form action="loop.php" method="post" class="upload__x" enctype="multipart/form-data">
                                            <input type="file" class="uploaex" name="xcelfile[]" accept=".xlsx,.xls" multiple="">
                                            <input type="text" name="txt">
                                            <input type="submit">
                                        </form>
                                        <progress value="-1" max="100"></progress>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
</div>

<!-- /.content-wrapper -->
