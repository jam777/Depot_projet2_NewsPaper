<?php
/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\AdminArticleManager;

/**
 * Class ItemController
 *
 */
class AdminArticleController extends AbstractController
{


    const CATEGORIES = ['cat1'=>['id'=>1,'name'=>'Sports'],
                        'cat2'=>['id'=>2,'name'=>'Politique'],
                        'cat3'=>['id'=>3,'name'=>'Environnement']];
    /**
     * Display article listing for admin
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function index()
    {
        $articleManager = new AdminArticleManager();
        $articles = $articleManager->selectAll();

        return $this->twig->render('AdminArticle/AdminArticleList.html.twig', ['articles' => $articles]);
    }

    /**
     * Handle article deletion
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $adminArticleManager = new AdminArticleManager();
        $adminArticleManager->delete($id);
        header('Location:/adminArticle/index');
    }

    /**
     * Create a new article
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function add()
    {
        /** Verification ajout article **/
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->verifyInputs($_POST);
            $errors = $result['errors'];
            $values = $result['values'];

            if (!empty($result['errors'])) {
                return $this->twig->render(
                    'AdminArticle/adminArticleForm.html.twig',
                    [   'errors'=>$errors,
                        'values'=>$values,
                        'categories'=>self::CATEGORIES,
                        'title2'=>"Modification Article"]
                );
            }

            $adminArticleManager = new AdminArticleManager();

            $id = $adminArticleManager->insert($values);
            
            header('Location:/adminArticle/index');
        }

        return $this->twig->render(
            'AdminArticle/adminArticleForm.html.twig',
            ['categories'=>self::CATEGORIES,
            'values'=>['date'=>date("Y-m-j")],
            'title2'=>"NouvelArticle"]
        );
    }

    /**
     * update an existing article
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */

    public function update(int $id)
    {
        // si POST, vérifier les entrées
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->verifyInputs($_POST);
            $errors = $result['errors'];
            $values = $result['values'];

            if (!empty($result['errors'])) {
                return $this->twig->render(
                    'AdminArticle/adminArticleForm.html.twig',
                    [   'errors'=>$errors,
                        'values'=>$values,
                        'categories'=>self::CATEGORIES,
                        'title2'=>"NouvelArticle"]
                );
            }
            $adminArticleManager = new AdminArticleManager();
            $adminArticleManager->edit($values);
            header('Location:/adminArticle/index');
        }

        $articleManager = new AdminArticleManager();
        $article = $articleManager->selectOneById($id);
        return $this->twig->render(
            'AdminArticle/adminArticleForm.html.twig',
            [   'values' => $article,
                'categories'=>self::CATEGORIES,
                'title2'=>"NouvelArticle"]
        );
    }

    /**
     *
     */
    private function verifyInputs($inputData)
    {
            $value = [];
            $error = [];

        $value['id']=$this->testInput($inputData["id"]);
        /** Verification title **/
        if (empty($inputData["title"])) {
            $error['title'] = 'Add title';
        } else {
             $value["title"] = $this->testInput($inputData["title"]);
        }
        /** Verification date **/
        if (empty($inputData["date"])) {
            $error['date'] = 'Add date';
        } else {
             $value["date"] = $this->testInput($inputData["date"]);
        }
        /** Verification author **/
        if (empty($inputData["author"])) {
            $error['author'] = 'Add author';
        } else {
             $value["author"] = $this->testInput($inputData["author"]);
        }
        /** Verification Category **/
        if (empty($inputData["category"])) {
            $error['category'] = 'Select Category';
        } else {
             $value["category"] = $this->testInput($inputData["category"]);
        }
        /** Verification Short text **/
        if (empty($inputData["shortText"])) {
            $error['shortText'] = 'Add short text';
        } else {
             $value["shortText"] = $this->testInput($inputData["shortText"]);
        }


        if (empty($inputData["content"])) {
            $error['content'] = 'Add short text';
        } else {
             $value["content"] = $inputData["content"];
        }
        
        /** Verification tag **/
        if (empty($inputData["tag"])) {
            $error['tag'] = 'Add tag';
        } else {
             $value["tag"] = $this->testInput($inputData["tag"]);
        }
            return ['errors'=>$error,'values'=>$value];
    }




    /**
     * Verify imputs from form
     *
     * @return string of cleaned input
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    private function testInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}
