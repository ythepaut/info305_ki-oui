<div class="modal fade" id="modalDirectDownload" tabindex="-1" role="dialog" aria-labelledby="modalDirectDownload" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">TÉLÉCHARGEMENT DU FICHIER</h4>
                    </div>
                    <div class="col-lg-1">

                    </div>

                    <div class="col-lg">


                        <span>Fichier à télécharger :</span><br />
                        <span style="text-align: center;" id="originalName-directdownload"></span>
                        <br />

                        <form id="directDownloadForm" action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax" style="text-align: center;">
                            <input type="hidden" name="action" value="download-file" />
                            <input type="hidden" name="filename" value="" id="path-directdownload" />
                            <input type="hidden" name="filekey" value="" id="key-directdownload" />

                            <div class="inner col-lg-4">
                                <h2> Télécharger </h2>
                                <a class="input-group-text label-icon" data-dismiss="modal" href='#' onclick='document.getElementById("directDownloadForm").submit()'><i class='fas fa-download edit'></i></a>
                            </div>
                        </form>

                        <br />

                        <div style="text-align: center;">
                            <button type="button" data-dismiss="modal">Fermer</button>
                        </div>

                    </div>

                    <div class="col-lg-1">

                    </div>
                </div>
                </div>

            </div>


        </div>

    </div>
</div>
