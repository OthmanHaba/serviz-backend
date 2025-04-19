<?php

namespace App\Filament\Resources\SupportSessionResource\Pages;

use App\Enums\FlagEnum;
use App\Filament\Resources\SupportSessionResource;
use App\Models\SupportMessage;
use App\Models\SupportSession;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\NoReturn;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class SupportChat extends Page
{
    use InteractsWithRecord;

    public string $newMessage;

    protected static string $resource = SupportSessionResource::class;

    protected static string $view = 'filament.resources.support-session-resource.pages.support-chat';


    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    public function sendNewMessage() : void
    {
        $this->record->supportMessages()->create([
           'message' => $this->newMessage,
           'sender_id' => Auth::id(),
        ]);

        $this->newMessage = '';
    }

    public function setIsClosed()
    {
        $this->record->update([
            'status' => FlagEnum::CLOSE
        ]);

        $this->redirect(route('filament.admin.resources.support-sessions.index'));
    }

    public static function getSlug(): string
    {
        return '{record}/support-chat';
    }
}
