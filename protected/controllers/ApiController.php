<?php
/**
 * Created by Only Love.
 * Date: 9/24/13
 * Time: 9:46 AM
 */

class ApiController extends Controller{
    const   API_KEY = 'voice_20131227';

    public function filters() {
        return array();
    }

    public function beforeAction($action){
        if(
            $action->id != "listReport"
            && $action->id != "htmlContent"
            && $action->id != "level"
            && $action->id != "class"
            && $action->id != "loginGoogleAndroid"
            && $action->id != "loginGoogle"
            && $action->id != "test"
            && $action->id != "test2"
            && $action->id != "test3"
            && $action->id != "partner"
            && $action->id != "loginFaceBook"
            && $action->id != "resetPass"
            && $action->id != "login"
            && $action->id != "register"
            && $action->id != "debug"
            && $action->id != "listSubjectVideo"
            && $action->id != "listChapterVideo"
        ){
            $sessionKey = isset($_POST['sessionkey']) ? $_POST['sessionkey'] : null;
            if($sessionKey == null){
                $sessionKey = isset($_GET['sessionkey']) ? $_GET['sessionkey'] : null;
            }
            $sessionKey = str_replace(' ','+',$sessionKey);
            Yii::log("\n SessionKey: ".$sessionKey);
            if(!CUtils::checkAuthSessionKey($sessionKey)) {
                ContentResponse::getErrorMessage(SESSION_KEY_INVALID, "cardCode");
                return false;
            }
        }else{
            return true;
        }
        return parent::beforeAction($action);
    }

    public function actions() {
        return array(
            /*
             * debug: viet cac funtion test
             */
            'debug' => 'protected.controllers.api.DebugAction',
            'login' => 'protected.controllers.api.LoginAction',
            'register' => 'protected.controllers.api.RegisterAction',
            'changepassword' => 'protected.controllers.api.ChangePassAction',
            'question' => 'protected.controllers.api.QuestionAction',
            'subject' => 'protected.controllers.api.SubjectAction',
            'class' => 'protected.controllers.api.ClassAction',
            'confilmLogin' => 'protected.controllers.api.ConfilmLoginAction',
            'listquestion' => 'protected.controllers.api.ListquestionAction',
            'useCard' => 'protected.controllers.api.UseCardAction',
            'useCardNet2e' => 'protected.controllers.api.UseCardNet2EAction',
            'gettrans' => 'protected.controllers.api.TransactionDetailAction',
            'listservice' => 'protected.controllers.api.ListserviceAction',
            'registerService' => 'protected.controllers.api.RegisterserivceAction',
            'cancelService' => 'protected.controllers.api.CancelserivceAction',
            'getDeviceToken' => 'protected.controllers.api.GetdevicetokenAction',
            'insertAvatar' => 'protected.controllers.api.InsertAvatarAction',
            'likeQuestion' => 'protected.controllers.api.LikequestionAction',
            'insertComment' => 'protected.controllers.api.InsertCommentAction',
            'answer' => 'protected.controllers.api.AnswerAction',
            'detailquestion' => 'protected.controllers.api.DetailquestionAction',
            'updateProfile' => 'protected.controllers.api.UpdateProfileAction',
            'listProfile' => 'protected.controllers.api.ListProfileAction',
            'updateStatus' => 'protected.controllers.api.UpdateStatusAction',
            'htmlContent' => 'protected.controllers.api.HtmlcontentAction',
            'holdQuestion' => 'protected.controllers.api.HoldQuestionAction',
            'resetPass' => 'protected.controllers.api.ResetPassAction',
            'loginFaceBook' => 'protected.controllers.api.LoginFaceBookAction',
            'loginGoogle' => 'protected.controllers.api.LoginGoogleAction',
            'loginGoogleAndroid' => 'protected.controllers.api.LoginGoogleAndroidAction',
            'partner' => 'protected.controllers.api.PartnerAction',
            'getNotifi' => 'protected.controllers.api.GetNotifiAction',
            'confirmLogin' => 'protected.controllers.api.ConfirmLoginAction',
            'test' => 'protected.controllers.api.TestAction',
            'test2' => 'protected.controllers.api.Test2Action',
            'test3' => 'protected.controllers.api.Test3Action',
            'banner' => 'protected.controllers.api.BannerAction',
            'searchQuestion' => 'protected.controllers.api.SearchQuestionAction',
            'detailQuestionBank' => 'protected.controllers.api.DetailQuestionBankAction',
            'detailquestionBk' => 'protected.controllers.api.DetailquestionBkAction',
            'listQuestionBank' => 'protected.controllers.api.ListQuestionBankAction',
            'level' => 'protected.controllers.api.LevelAction',
            'confirmRegisterService' => 'protected.controllers.api.ConfirmRegisterserivceAction',
            'history' => 'protected.controllers.api.HistoryAction',
            'report' => 'protected.controllers.api.ReportAction',
            'confirmLevel' => 'protected.controllers.api.ConfirmLevelAction',
            'changeLevel' => 'protected.controllers.api.ChangeLevelAction',
            'questionTeacher' => 'protected.controllers.api.QuestionTeacherAction',
            'questionPoint' => 'protected.controllers.api.QuestionPointAction',
            'exam' => 'protected.controllers.api.ExamAction',
            'typeAccount' => 'protected.controllers.api.TypeAccountAction',
            'questionServer' => 'protected.controllers.api.QuestionServerAction',
            'listReport' => 'protected.controllers.api.ListReportAction',
            'listCategoryBlog' => 'protected.controllers.api.CategoryBlogAction',
            'listBlog' => 'protected.controllers.api.ListBlogAction',
            'detailBlog' => 'protected.controllers.api.DetailBlogAction',
            'listSubjectVideo' => 'protected.controllers.api.ListSubjectVideoAction',
            'listChapterVideo' => 'protected.controllers.api.ListChapterVideoAction',
            'listVideo' => 'protected.controllers.api.ListVideoAction',
            'detailVideo' => 'protected.controllers.api.DetailVideoAction',
            'statusComment' => 'protected.controllers.api.StatusCommentAction',
        );
    }

    public static function getStatusCodeMessage($status) {
        $codes = array(
            200 => 'OK',
            400 => 'ERROR: Bad request. API doesn\'t exist OR request failed due to some reason.',
        );

        return (isset($codes[$status])) ? $codes[$status] : null;
    }

    public static function sendResponse($status = 200, $body = '', $content_type = 'application/json') {
        header('HTTP/1.1 ' . $status . ' ' . self::getStatusCodeMessage($status));
        header('Content-type: ' . $content_type);
        if(trim($body) != '') echo $body;
        Yii::app()->end();
    }
}