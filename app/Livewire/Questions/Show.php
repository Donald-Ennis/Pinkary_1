<?php

declare(strict_types=1);

namespace App\Livewire\Questions;

use App\Models\Question;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

final class Show extends Component
{
    /**
     * The component's question ID.
     */
    #[Locked]
    public string $questionId;

    /**
     * The component's in index state.
     */
    #[Locked]
    public bool $inIndex = false;

    /**
     * Whether the pinned label should be displayed or not.
     */
    #[Locked]
    public bool $pinnable = false;

    /**
     * Indicate that the reply box should be shown.
     */
    public bool $replying = false;

    /**
     * Determine if this is currently being viewed in thread view.
     */
    #[Locked]
    public bool $threadView = false;

    /**
     * Which update we've come from to get here.
     */
    #[Url]
    public ?string $from;

    /**
     * Refresh the component.
     */
    #[On('question.updated')]
    #[On('question.created')]
    public function refresh(): void
    {
        //
    }

    /**
     * Get the listeners for the component.
     *
     * @return array<string, string>
     */
    public function getListeners(): array
    {
        return $this->inIndex ? [] : [
            'question.ignore' => 'ignore',
            'question.reported' => 'redirectToProfile',
        ];
    }

    /**
     * Redirect to the profile.
     */
    public function redirectToProfile(): void
    {
        $question = Question::findOrFail($this->questionId);

        $this->redirectRoute('profile.show', ['username' => $question->to->username], navigate: true);
    }

    /**
     * Ignores the question.
     */
    public function ignore(): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        if ($this->inIndex) {
            $this->dispatch('notification.created', message: 'Question ignored.');

            $this->dispatch('question.ignore', questionId: $this->questionId);

            return;
        }

        $question = Question::findOrFail($this->questionId);

        $this->authorize('ignore', $question);

        $question->update(['is_ignored' => true]);

        $this->redirectRoute('profile.show', ['username' => $question->to->username], navigate: true);
    }

    /**
     * Like the question.
     */
    public function like(): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $question = Question::findOrFail($this->questionId);

        $question->likes()->firstOrCreate([
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Pin a question.
     */
    public function pin(): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $user = type(auth()->user())->as(User::class);

        $question = Question::findOrFail($this->questionId);

        $this->authorize('pin', $question);

        Question::withoutTimestamps(fn () => $user->pinnedQuestion()->update(['pinned' => false]));
        Question::withoutTimestamps(fn () => $question->update(['pinned' => true]));

        $this->dispatch('question.updated');
    }

    /**
     * Unpin a pinned question.
     */
    public function unpin(): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $question = Question::findOrFail($this->questionId);

        $this->authorize('update', $question);

        Question::withoutTimestamps(fn () => $question->update(['pinned' => false]));

        $this->dispatch('question.updated');
    }

    /**
     * Unlike the question.
     */
    public function unlike(): void
    {
        if (! auth()->check()) {
            $this->redirectRoute('login', navigate: true);

            return;
        }

        $question = Question::findOrFail($this->questionId);

        if ($like = $question->likes()->where('user_id', auth()->id())->first()) {
            $this->authorize('delete', $like);

            $like->delete();
        }
    }

    public function shouldShowReplyBox(): bool
    {
        return $this->replying
            && $this->threadView
            && auth()->check();
    }

    public function shouldShowReplies(): bool
    {
        $question = Question::with(['children'])->findOrFail($this->questionId);

        return $question->children->isNotEmpty() && $this->threadView;
    }

    public function reply(): void
    {
        $question = Question::with(['to'])->findOrFail($this->questionId);

        $this->redirectRoute('questions.show', [
            'question' => $this->questionId,
            'username' => $question->to->username,
        ]);
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $question = Question::where('id', $this->questionId)
            ->with(['to', 'from', 'likes', 'parent', 'children'])
            ->withCount('likes')
            ->firstOrFail();

        return view('livewire.questions.show', [
            'user' => $question->to,
            'question' => $question,
        ]);
    }
}
