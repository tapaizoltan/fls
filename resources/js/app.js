import './bootstrap';

document.addEventListener('openUserModal', () => {
    Livewire.emit('openUserModal');
});