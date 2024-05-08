<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    public $name;
    public $search;
    public $editingTodoId;
    public $editingTodoName;

    public function createTodo()
    {
        $validate = $this->validate([
            'name' => 'required|min:3|max:50'
        ]);
        Todo::create($validate);
        $this->reset('name');
        session()->flash('success', 'Created');
        // $this->resetPage();
    }

    public function deleteTodo($todoId)
    {
        // try {
        //     Todo::findOrfail($todoId)->delete();
        // } catch (Exception $e) {
        //     session()->flash('error', 'Failed to delete Todo');
        //     return;
        // }
        if (Todo::find($todoId)) {
            Todo::find($todoId)->delete();
        } else {
            session()->flash('error', 'Failed to delete Todo');
            return;
        }
    }

    public function toggleTodo($todoId)
    {
        $todo = Todo::find($todoId);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function editTodo($todoId)
    {
        $this->editingTodoId = $todoId;
        $this->editingTodoName = Todo::find($todoId)->name;
    }

    public function updateTodo()
    {
        $this->validate([
            'editingTodoName' => 'required|min:3|max:50'
        ]);

        Todo::find($this->editingTodoId)->update(['name' => $this->editingTodoName]);

        $this->cancelEditing();
    }

    public function cancelEditing()
    {
        $this->reset('editingTodoId', 'editingTodoName');
    }

    public function render()
    {
        $todos = Todo::latest()->where('name', 'like', "%{$this->search}%")->paginate(3);
        return view('livewire.todo-list', [
            'todos' => $todos,
        ]);
    }
}
