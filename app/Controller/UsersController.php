<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {

	public function admin_login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				/**
				  * Acoが存在しない場合は、初期状態と判断しACLの設定画面に遷移させる
				  */
				$Aco = ClassRegistry::init('Aco');
				$result = $Aco->find('count');
				if (empty($result)) {
					return $this->redirect(array(
						'plugin' => 'acl',
						'controller' => 'acos',
						'action' => 'synchronize',
					));
				} else {
					return $this->redirect($this->Auth->redirect());
				}
			} else {
				$this->Session->setFlash('Your username or password was incorrect.');
			}
		}
	}

	public function admin_logout() {
		$this->redirect($this->Auth->logout());
	}


/**
 * beforeFilter method
 *
 * @return void
 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		/**
		 * ユーザーが存在しない場合、初期作成する処理に遷移させる
		 */
		if ( $this->User->find('count') == 0 ) {
			$this->createInitialUser();
		}
	}

/**
 * 初期ユーザーを生成
 *
 * @param void
 * @access public
 * @return void
 * @author Kaz Watanabe
 **/
	private function createInitialUser()
	{
		$this->Auth->allow('admin_add');
		$this->set('createInitialUser', true);
		if ( $this->action != 'admin_add') {
			$this->redirect(array('action' => 'admin_add'));
			return ;
		}

		$Group = ClassRegistry::init('Group');
		if ($Group->find('count') == 0) {
			$data = array(
				array(
					'name' => 'サイト管理者',
				),
				array(
					'name' => '一般ユーザー',
				),
			);

			$Group->saveAll($data);
		}
		$this->Session->setFlash(__('Please create the user account.'));
		return ;
	}

/**
 * index method
 *
 * @return void
 */
	public function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index', 'admin' => true));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
		$groups = $this->User->Group->find('list');
		$this->set(compact('groups'));
	}

/**
 * delete method
 *
 * @throws MethodNotAllowedException
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
