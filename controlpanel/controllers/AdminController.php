<?php

namespace MyAdmApp {

    use MyApp\MethodExecuter as Mexec;
    use MyApp\NavigateModel;
    use MyApp\View;

    class AdminController extends Mexec
    {
        private $header = [];
        private $content = [];
        private $footer = [];

        public function index()
        {
            if (UserAuth::isAuth()) {
                View::Render(ADM_VIEWS_PATH . "header" . EXT, null, $this->header);
                View::RenderAdm(ADM_VIEWS_PATH . "template" . EXT,
                                ADM_PAGES_PATH . "main" . EXT, [
                                    ADM_VIEWS_PATH.'navbartop'.EXT,
                                    ADM_VIEWS_PATH.'navbarsidebar'.EXT,
                                ], $this->content);
                View::Render(ADM_VIEWS_PATH . "footer" . EXT, null, $this->footer);
            } else {
                $this->loginin();
            }
        }

        public function loginin()
        {
            View::Render(ADM_VIEWS_PATH . "header" . EXT, null, $this->header);
            View::Render(ADM_VIEWS_PATH . "template" . EXT, ADM_PAGES_PATH . "loginin" . EXT, $this->content);
            View::Render(ADM_VIEWS_PATH . "footer" . EXT, null, $this->footer);
        }

        public function register()
        {
            View::Render(ADM_VIEWS_PATH . "header" . EXT, null, $this->header);
            View::Render(ADM_VIEWS_PATH . "template" . EXT, ADM_PAGES_PATH . "register" . EXT, $this->content);
            View::Render(ADM_VIEWS_PATH . "footer" . EXT, null, $this->footer);
        }
        public function options(){
            if (UserAuth::isAuth()) {
                View::Render(ADM_VIEWS_PATH . "header" . EXT, null, $this->header);
                View::RenderAdm(ADM_VIEWS_PATH . "template" . EXT,
                    ADM_PAGES_PATH . "options" . EXT, [
                        ADM_VIEWS_PATH.'navbartop'.EXT,
                        ADM_VIEWS_PATH.'navbarsidebar'.EXT,
                    ], $this->content);
                View::Render(ADM_VIEWS_PATH . "footer" . EXT, null, $this->footer);
            } else {
                $this->loginin();
            }
        }
        public function navbar(){
            if (UserAuth::isAuth()) {
                //$navM = new NavigateModel();
                //$this->content['navigate'] = $navM->getManyRows([], 'id', 'ASC', 100);

                View::Render(ADM_VIEWS_PATH . "header" . EXT, null, $this->header);
                View::RenderAdm(ADM_VIEWS_PATH . "template" . EXT,
                    ADM_PAGES_PATH . "navbar" . EXT, [
                        ADM_VIEWS_PATH.'navbartop'.EXT,
                        ADM_VIEWS_PATH.'navbarsidebar'.EXT,
                    ], $this->content);
                View::Render(ADM_VIEWS_PATH . "footer" . EXT, null, $this->footer);
            } else {
                $this->loginin();
            }
        }
        public function logout()
        {
            UserAuth::userSessionDestroy();
            header('Location: /controlpanel/admin');
            exit;
        }
        public function registeruser() {

            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $login = htmlspecialchars(trim($_POST['login']));
                $email = htmlspecialchars(trim($_POST['email']));
                $password = htmlspecialchars(trim($_POST['password']));
                $cpassword = htmlspecialchars(trim($_POST['cpassword']));
                if($password == $cpassword) {
                    $password = hash("sha256", $password);
                    //varSuperDump($login."+".$email."+".$password);
                    $userM = new UserModel();
                    if($userM->createNewUser($login, $email, $password)) {
                        $this->content['success'] = "Добро пожаловать, авторизуйтесь";
                        $this->loginin();
                        exit;
                    }
                }
            }
            $this->content['error'] = "Операция неуспешна. Введены некорректные данные";
            $this->register();
        }
        public function checkuser()
        {
            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $email = htmlspecialchars(trim($_POST['email']));
                $password = htmlspecialchars(trim($_POST['password']));
                $password = hash("sha256", $password);

                $userM = new UserModel();
                $user = $userM->checkUser($email, $password);

                if ($user == null) {
                    $this->content['error'] = "В доступе отказано, попробуйте ещё";
                    $this->loginin();
                } else {
                    //session_start();
                    $_SESSION['login'] = $user['login'];
                    $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
                    header('Location: /controlpanel/admin');
                    exit;
                }
            }
        }
    }
}