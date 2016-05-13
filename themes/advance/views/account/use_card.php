<?php
/** @var $this AccountController */
$action =  Yii::app()->controller->action->id;
$this->pageTitle = "Nạp thẻ";
?>
                <div class="web_body">
                   <div class="row col-md-12 col-xs-12 col-lg-12"  style="margin-bottom: 15px;">
                        <div class="col-md-6 col-xs-6 col-lg-6 tab-card <?php if($action == 'useCard'){ echo 'activeHistory' ;}?>">
                            <a href="<?php echo Yii::app()->baseUrl .'/account/useCard'?>">Nạp thẻ</a>
                        </div>
                <!--        <div class="col-md-6 col-xs-6 col-lg-6 tab-historyService <?php if($action == 'historyService'){ echo 'activeHistory' ;}?>">
                             <a href="<?php // echo Yii::app()->baseUrl .'/account/historyService'?>">Lịch sử mua gói</a>
                        </div>-->
                        <div class="col-md-6 col-xs-6 col-lg-6 tab-historyCard <?php if($action == 'historyCard'){ echo 'activeHistory' ;}?>">
                             <a href="<?php echo Yii::app()->baseUrl .'/account/historyCard'?>">Lịch sử nạp thẻ</a>
                        </div>
                    </div>
                <form name="update_email_form" id="update_email_form" method="POST" style="margin-top: 50px;"
					action="<?php echo $this->createUrl("/account/useCard"); ?>">
                    <div class="listarticle service">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <label>Loại thẻ:</label>
                                        <select class="form-control" name="issuer">
                                            <!--Hoc de-->
<!--                                            <option value="VT">Viettel</option>
                                            <option value="MOBI">MobiFone</option>
                                            <option value="VINA">VinaFone</option>-->
                                              <!--net2e-->
                                            <option value="VIETTELCARD">Viettel</option>
                                            <option value="MOBICARD">MobiFone</option>
                                            <option value="VINACARD">VinaFone</option>
                                            <option value="ONCASH">OnCash</option>
                                            <option value="HOCDE">Hocde</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label>Seri:</label>
                                            <input type="text" class="form-control" name="cardSerial" id="card_seria" placeholder="Nhập seri thẻ">
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label>Mã thẻ::</label>
                                            <input type="text" class="form-control" name="cardCode" id="card_code" placeholder="Nhập mã thẻ">
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <?php if(isset($message)){?>
                                        <?php echo $message?>
                                        <?php }?>
                                    </div>
                                    <div class="nt-footer">
                                        <button type="submit" class="btn btn-primary">Nạp thẻ</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
	</form>
                </div>
                <div class="web_body">
                    <div class="contentHtml">
                        <?php 
                        foreach ($html as $html){
                            if($html['type'] == 2){
                            ?>
                            <p><?php echo $html['content']?></p>  
                            <?php
                                }
                            ?>
                        <?php }?>
                        
                    </div>
                </div>
<style>
    .web_body td{border: 1px solid #ccc; text-align: center;padding: 5px; font-size: 15px;}
    .activeHistory{background: #55CAF5;}
</style>