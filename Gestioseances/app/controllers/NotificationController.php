<?php

class NotificationController extends Controller
{
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationModel = new Notification();
    }

    public function index(): void
    {
        $this->requireLogin();

        $userId = $this->getUserId();
        $notifications = $this->notificationModel->findByUser($userId);
        $countNonLues = $this->notificationModel->countNonLues($userId);

        $this->view('notifications/index', [
            'notifications' => $notifications,
            'countNonLues' => $countNonLues,
            'flash' => $this->getFlash()
        ]);
    }

    public function marquerLue(int $id): void
    {
        $this->requireLogin();

        $notification = $this->notificationModel->find($id);

        if (!$notification || $notification['user_id'] !== $this->getUserId()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Notification non trouvée.'], 404);
            }
            $this->redirect('/notifications');
        }

        $this->notificationModel->marquerCommeLue($id);

        if ($this->isAjax()) {
            $this->json(['success' => true]);
        }

        if ($notification['demande_id']) {
            $this->redirect('/demandes/' . $notification['demande_id']);
        }

        $this->redirect('/notifications');
    }

    public function marquerToutesLues(): void
    {
        $this->requireLogin();

        $this->notificationModel->marquerToutesCommeLues($this->getUserId());

        if ($this->isAjax()) {
            $this->json(['success' => true]);
        }

        $this->setFlash('success', 'Toutes les notifications ont été marquées comme lues.');
        $this->redirect('/notifications');
    }

    public function count(): void
    {
        $this->requireLogin();

        $count = $this->notificationModel->countNonLues($this->getUserId());

        $this->json(['count' => $count]);
    }

    public function recent(): void
    {
        $this->requireLogin();

        $notifications = $this->notificationModel->findNonLues($this->getUserId());

        $notifications = array_slice($notifications, 0, 5);

        $formatted = array_map(function($notif) {
            return [
                'id' => $notif['id'],
                'titre' => $notif['titre'],
                'message' => $notif['message'],
                'type' => $notif['type'],
                'demande_id' => $notif['demande_id'],
                'date_relative' => Notification::formatDateRelative($notif['created_at']),
                'icone' => Notification::getIcone($notif['type']),
                'classe' => Notification::getTypeClass($notif['type'])
            ];
        }, $notifications);

        $this->json([
            'notifications' => $formatted,
            'total' => $this->notificationModel->countNonLues($this->getUserId())
        ]);
    }

    public function supprimer(int $id): void
    {
        $this->requireLogin();

        $notification = $this->notificationModel->find($id);

        if (!$notification || $notification['user_id'] !== $this->getUserId()) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Notification non trouvée.'], 404);
            }
            $this->setFlash('danger', 'Notification non trouvée.');
            $this->redirect('/notifications');
        }

        $this->notificationModel->delete($id);

        if ($this->isAjax()) {
            $this->json(['success' => true]);
        }

        $this->setFlash('success', 'Notification supprimée.');
        $this->redirect('/notifications');
    }
}
