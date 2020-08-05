<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskList;
use App\Repository\TaskListRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;

class ListController extends AbstractFOSRestController
{
    /**
     * @var $taskListRepository
     */
    private $taskListRepository;
     /**
     * @var $entityManagerInterface
     */
    private $entityManagerInterface;

       /**
     * @var $taskRepository
     */
    private $taskRepository;

    public function __construct(TaskListRepository $taskListRepository, TaskRepository $taskRepository, EntityManagerInterface $entityManagerInterface)
    {
        $this->taskListRepository= $taskListRepository;
        $this->entityManagerInterface= $entityManagerInterface;
        $this->tasktRepository= $taskRepository;
    }

      /**
     * @return "FOS\RestBundle\View\View"
     */

   public function getListsAction(){
    $data= $this->taskListRepository->findAll();
    return $this->view($data, Response::HTTP_OK);
}

    /**
     * @return "FOS\RestBundle\View\View"
     */

   public function getListAction(TaskList $list){

        return $this->view($list, Response::HTTP_OK);
   }
    

    /* get list task of one TaskList */
    /**
     * @return ""FOS\RestBundle\View\View"
     * @param TaskList $list
     */

    public function getListsTaskAction(TaskList $list){
        $data= $list;
        if($list){
            $task= $data->getTasks();
            return $this->view($task, Response::HTTP_OK);
        }
    }

    /**
     * @Rest\RequestParam(name="title", description="title of list", nullable= false)
     * @param ParamFetcher $paramFetcher
     * @param TaskList $list
     * @return "FOS\RestBundle\View\View"
     */
   public function postListsAction(ParamFetcher $paramFetcher){
        $title= $paramFetcher->get('title');
        if($title){
            $list= new TaskList();
            $list->setTitle($title);
            $this->entityManagerInterface->persist($list);
            $this->entityManagerInterface->flush();
            return $this->view($list, Response::HTTP_OK);
        }
        return $this->view(["title"=>"title not null"], Response::HTTP_BAD_REQUEST);
   }

   public function putListsAction(){


   }

   /**
    * @Rest\FileParam(name="image", description=" background the list", nullable=false, image=true)
    * @param TaskList $list
    * @param ParamFetcher $paramFetcher
    * @param Request $request
    * @return "FOS\RestBundle\View\View"
    */

   public function backgroundListAction(Request $request, ParamFetcher $paramFetcher, TaskList $list){
       $data=$list;
       $currentBackground= $data->getBackground();
       if(!is_null($currentBackground)){
            $filesysteme= new Filesystem();
            $filesysteme->remove(
                $this->getUploadDir . $currentBackground
            );
       }
       /** @var UploadedFile $file */
    $file= ($paramFetcher->get('image'));
    if($file){
        $filename= md5(uniqid()). '.' . $file->guestClientExtension();
        $file->move(
            $this->getUploadDir(),
            $filename
        );
        $data->setBackground($filename);
        $data->setBackgroundPath('/uploads/'.$filename);
        $this->entityManagerInterface->persist($data);
        $this->entityManagerInterface->flush();
    
        $dat= $request->getUriForPath($list->getBackground());
            return $this->view($dat, Response::HTTP_OK);
    }
            return $this->view(["message"=>"something !!!!!!!!"]);
   }

    private function getUploadDir(){
     return $this->getParameter('uploads_dir');
 }
 
 /**
     * @return ""FOS\RestBundle\View\View"
     * @param TaskList $list
     */
 public function deleteListAction(TaskList $list){
$data= $list;
    if($data){
        $this->entityManagerInterface->remove($data);
        $this->entityManagerInterface->flush();
    }
    return $this->view(null, Response::HTTP_NO_CONTENT);
 }
/**
 * @Rest\RequestParam(name="title", description="new title of this list", nullable=false)
 * @param ParamFetcher $paramFetcher
 * @param TaskList $list
 * @return "FOS\RestBundle\View\View"
 */
 public function patchListTitleAction(ParamFetcher $paramFetcher, TaskList $list){
    $errors= [];
    $data= $list;
     $title= $paramFetcher->get('title');
     if(trim($title) !== ''){
        if($data){
            $data->setTitle($title);
            $data->entityManagerInterface->persist($title);
            $data->entityManagerInterface->flush();
            return $this->view(null, Response::HTTP_NO_CONTENT); 
        }
        $errors =[
            "title"=>"list not found!"
        ];
     }
     $errors = [
         "message"=>"this value can't be empty"
     ];
     return $this->view($errors, Response::HTTP_OK);
 }

 /**
  * @Rest\RequestParam(name="title", description=" task of this list", nullable= false)
  * @param ParamFetcher $paramFetcher
  * @return "FOS\RestBundle\View\View"
  * @param TaskList $list
  */

 public function postListTaskAction(ParamFetcher $paramFetcher, TaskList $list){
     /* create Task of one TastList */
    if($list){
        $title = $paramFetcher->get('title');
        $task = new Task();
        $task->setTitle($title);
        $task->setList($list);/* id taskList */
        $list->addTask($task);

        $this->entityManagerInterface->persist($task);
        $this->entityManagerInterface->flush();
        return $this->view($task, Response::HTTP_OK);
    }
    return $this->view(['errors'=>'something went wrong'], Response::HTTP_NO_CONTENT);
 }
}
