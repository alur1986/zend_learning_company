<?php

namespace Learning\Taxonomy\Controller;

use Savve\Stdlib;
use Savve\Stdlib\Exception;
use Savve\Mvc\Controller\AbstractActionController;
use Zend\Http\Header\SetCookie as SetCookie;
use Zend\Http\Header\Cookie;
use Zend\Http\Request;

class CategoryController extends AbstractActionController
{

    /**
     * Display ALL categories
     */
    public function directoryAction ()
    {
        $siteId     = $this->params('site_id');
        $service    = $this->categoryService();
        $categories = $service->findAllCategoriesBySiteId($siteId);
        $message    = false;

        // if a success message is passed
        $directoryMessageSuccess = false;
        if (isset($this->getRequest()->getCookie()->directoryMessageSuccess)) {
            $directoryMessageSuccess = $this->getRequest()->getCookie()->directoryMessageSuccess;
        }
        if ($directoryMessageSuccess) {
            $message['success'] = $this->translate($directoryMessageSuccess);
        }
        // if a success message is passed
        $directoryMessageError = false;
        if (isset($this->getRequest()->getCookie()->directoryMessageError)) {
            $directoryMessageError = $this->getRequest()->getCookie()->directoryMessageError;
        }
        if ($directoryMessageError) {
            $message['error'] = $this->translate($directoryMessageError);
        }

        return [
            'categories' => $categories,
            'message'    => $message
        ];
    }

    /**
     * Create ONE category
     */
    public function createAction ()
    {
        $siteId   = $this->params('site_id');
        $service  = $this->categoryService();
        $request  = $this->getRequest();
        $message = false;

        // form
        $form = $this->form('Learning\Taxonomy\Form\CreateCategory');

        // process form request
        if ($post = $this->post(false)) {
            try {
                // validate form
                $data = $form->validate($post);

                // create category in repository
                $category = $service->createCategory($data, $siteId);

                // success
                $message = $this->translate('The category has been created successfully.');
                $cookie = new SetCookie('updateMessage', $message, time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                }
                return $this->redirect()->toRoute('learning/category/update', ['category_id' => $category['taxonomy_id']]);
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = $this->translate('Cannot create the new category. An internal error has occurred. Please contact your support administrator or try again later.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }

        return [
            'form'    => $form,
            'message' => $message
        ];
    }

    /**
     * Display ONE category
     */
    public function readAction ()
    {
        $categoryId = $this->params('category_id');
        $service = $this->categoryService();
        $category = $service->findOneCategoryById($categoryId);

        return [
            'category' => $category
        ];
    }

    /**
     * Update ONE category
     */
    public function updateAction ()
    {
        $categoryId = $this->params('category_id');
        $service    = $this->categoryService();
        $category   = $service->findOneCategoryById($categoryId);

        // form
        $form       = $this->form('Learning\Taxonomy\Form\CreateCategory');

        $request    = $this->getRequest();
        $message    = false;

        // if a success message is passed
        $updateMessage = false;
        if (isset($this->getRequest()->getCookie()->updateMessage)) {
            $updateMessage = $this->getRequest()->getCookie()->updateMessage;
        }
        if ($updateMessage) {
            $message['success'] = $this->translate($updateMessage);
        }

        // process form request
        if ($data = $this->post(false)) {
            try {

                // pre validate - the $form->validator is stripping out the 'taxonomy_id for some reason so I wont run it unless required
                if ($data['taxonomy_id'] == '' || $data['term'] == '') {
                   $data = $form->validate($data);
                }

                // update category in repository
                $category = $service->updateCategory($data);

                // success
                $message['success'] = $this->translate('Updated the category successfully.');
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message['success']);
                    return $this->redirect()->refresh();
                }
            }
            catch (Exception\InvalidFormException $e) {
                // form validation exception, do nothing
            }
            catch (\Exception $e) {
                // failed
                $message['error'] = sprintf($this->translate('Cannot update the category "%s". An internal error has occurred. Please contact your support administrator or try again later.'), $category['term']);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message['error']);
                    return $this->redirect()->refresh();
                }
            }
        }
        $form->bind($category);

        return [
            'category' => $category,
            'form'     => $form,
            'message'  => $message
        ];
    }

    /**
     * Delete ONE category
     */
    public function deleteAction ()
    {
        $categoryId = $this->params('category_id');
        $service  = $this->categoryService();
        $category = $service->findOneCategoryById($categoryId);
        $request  = $this->getRequest();

        // process request
        if ($this->params('confirm') == 'yes') {
            try {
                // delete category
                $service->deleteCategory($categoryId);

                // success
                $message = $this->translate('The category has been deleted successfully.');
                $cookie = new SetCookie('directoryMessageSuccess', $message, time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addSuccessMessage($message);
                }
                return $this->redirect()->toRoute('learning/category/directory');
            }
            catch (\Exception $e) {
                // failed
                $message = sprintf($this->translate('Cannot delete the category "%s". An internal error has occurred. Please contact your support administrator or try again later.'), $category['term']);
                $cookie = new SetCookie('directoryMessageError', $message, time() + 60 * 1, '/');
                $this->getResponse()->getHeaders()->addHeader($cookie);
                if (!$request->isXmlHttpRequest()) {
                    $this->flashMessenger()->addErrorMessage($message);
                }
                return $this->redirect()->toRoute('learning/category/directory');
            }
        }

        return [
            'category' => $category
        ];
    }

    /**
     * Display ONE category by slug
     * Display ALL items related to the category
     */
    public function permalinkAction ()
    {
        $slug = $this->params('slug');
        $service = $this->categoryService();

        // find category
        $category = $service->findOneCategoryBySlug($slug);

        return [
            'category' => $category,
        ];
    }

    /**
     * Get the Learning Category Service
     *
     * @return \Learning\Taxonomy\Service\CategoryService
     */
    public function categoryService ()
    {
        return $this->service('Learning\Taxonomy\CategoryService');
    }
}