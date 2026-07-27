export const avatarColors = [
    'bg-primary-container text-on-primary-container',
    'bg-secondary-container text-on-secondary-container',
    'bg-tertiary-container text-on-tertiary-container',
];

export function getAvatarColor(index: number): string {
    return avatarColors[index % avatarColors.length];
}

export function getRoleBadgeClasses(role: string): string {
    switch (role) {
        case 'Administrator':
            return 'bg-primary-fixed text-on-primary-fixed-variant';
        case 'Staff':
            return 'bg-tertiary-fixed text-on-tertiary-fixed-variant';
        default:
            return 'bg-secondary-fixed text-on-secondary-fixed-variant';
    }
}
