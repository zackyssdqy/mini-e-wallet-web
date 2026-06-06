import { Link } from '@inertiajs/react';

function PageButton({ href, active, children, disabled = false }) {
    const baseClasses =
        'inline-flex items-center rounded-md border px-3 py-2 text-sm font-medium transition';
    const activeClasses = active
        ? 'border-gray-900 bg-gray-900 text-white'
        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50';

    if (!href) {
        return (
            <span
                className={`${baseClasses} ${activeClasses} ${disabled ? 'cursor-not-allowed opacity-50' : ''}`}
            >
                {children}
            </span>
        );
    }

    return (
        <Link
            href={href}
            preserveScroll
            className={`${baseClasses} ${activeClasses} ${disabled ? 'cursor-not-allowed opacity-50' : ''}`}
        >
            {children}
        </Link>
    );
}

export default function Pagination({ links }) {
    if (!links || links.length <= 3) {
        return null;
    }

    return (
        <div className="flex flex-wrap items-center gap-2">
            {links.map((link, index) => (
                <PageButton
                    key={`${link.label}-${index}`}
                    href={link.url}
                    active={link.active}
                    disabled={!link.url}
                >
                    <span dangerouslySetInnerHTML={{ __html: link.label }} />
                </PageButton>
            ))}
        </div>
    );
}
