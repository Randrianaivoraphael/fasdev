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
use Symfony\Component\Validator\Constraints\Uuid;
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
     * @param int $id
     */

    public function getListAction(int $id){
        $data= $this->laskListRepository->findOneBy(['is'=>$id,]);
        return $this->view($data, Response::HTTP_OK);

    }

    public function getListsTaskAction(int $id){

    }

    /**
     * @Rest\RequestParam(name="title", description="title of list", nullable= false)
     * @param ParamFetcher $paramFetcher
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
    * @Rest\FileParam(name="image", description=" background the list", nullable=false, image= true)
    * @param $id
    * @param ParamFetcher $paramFetcher
    * @param Request $request
    * @return "FOS\RestBundle\View\View"
    */

   public function backgroundListAction(Request $request, ParamFetcher $paramFetcher, $id){
       $list= $this->taskListRepository->findOneBy(['id'=>$id]);
       $currentBackground= $list->getBackground();
       if(!is_null($currentBackground)){
            $filesysteme= new Filesystem();
            $filesysteme->remove(
                $this->getUploadDir .$currentBackground
            );
       }
    $file= ($paramFetcher->get('image'));
    if($file){
        $filename= md5(uniqid()). '.' . $file->guestClientExtension();
        $file->move(
            $this->getUploadDir(),
            $filename
        );
        $list->setBackground($filename);
        $list->setBackgroundPath('/uploads/'.$filename);
        $this->entityManagerInterface->persist($list);
        $this->entityManagerInterface->flush();
    
        $data= $request->getUriForPath($list->getBackground());
            return $this->view($data, Response::HTTP_OK);
    }
            return $this->view(["message"=>"something !!!!!!!!"]);
   }

    private function getUploadDir(){
     return $this->getParameter('uploads_dir');
 }
 /**
  * @param int $id
  */

 public function deleteListAction(int $id){
    $list= $this->taskListRepository->findOneBy(["id"=>$id]);
    if($list){
        $this->entityManagerInterface->remove($list);
        $this->entityManagerInterface->flush();
    }
    return $this->view(null, Response::HTTP_NO_CONTENT);
 }
/**
 * @param int $id
 * @param ParamFetcher $paramFetcher
 * @Rest\RequestParam(name="title", description="new title of this list", nullable=false)
 * @return "FOS\RestBundle\View\View"
 */
 public function patchListTitleAction(ParamFetcher $paramFetcher, int $id){
    $errors= [];
     $list= $this->taskListRepository->findOnBy(["id"=>$id]);
     $title= $paramFetcher->get('title');
     if(trim($title) !== ''){
        if($list){
            $list->setTitle($title);
            $list->entityManagerInterface->persist($title);
            $list->entityManagerInterface->flush();
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
  * @param int $id
  * @Rest\RequestParam(name="title", description=" task of this list", nullable= false)
  * @param ParamFetcher $paramFetcher
  * @return "FOS\RestBundle\View\View"
  */
 public function postListTaskAction(ParamFetcher $paramFetcher, int $id){
    $list= $this->taskListRepository->findOneBy(["id" =>$id]);
    if($list){
        $title = $paramFetcher->get('title');
        $task = new Task();
        $task->setTitle($title);
        $task->setList($list);
        $list->addTask($task);
        $this->entityManagerInterface->persist($list);
        $this->entityManagerInterface->flash();
        return $this->view($task, Response::HTTP_OK);
    }
    return $this->view(['errors'=>'something went wrong'], Response::HTTP_NO_CONTENT);
 }

  /**
  * @param int $id
  * @param ParamFetcher $paramFetcher
  * @return "FOS\RestBundle\View\View"
  */
  public function removeListTaskAction(int $id){
    $task= $this->taskRepository->findOneBy(['id' =>$id]);
    if($task){
        $this->entityManagerInterface->remove($task);
        $this->entityManagerInterface->flash();
        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
    return $this->view(['errors'=>'something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
 }
}
