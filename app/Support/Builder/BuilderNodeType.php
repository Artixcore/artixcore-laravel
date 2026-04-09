<?php

namespace App\Support\Builder;

/**
 * Visual builder node `type` values (document JSON).
 */
enum BuilderNodeType: string
{
    case Root = 'root';
    case Section = 'section';
    case Columns = 'columns';
    case Column = 'column';
    case Spacer = 'spacer';
    case Divider = 'divider';
    case Hero = 'hero';
    case Heading = 'heading';
    case Paragraph = 'paragraph';
    case Image = 'image';
    case Button = 'button';
    case FeatureGrid = 'feature_grid';
    case Cta = 'cta';
    case RichText = 'rich_text';
    case TestimonialGrid = 'testimonial_grid';
    case PricingTable = 'pricing_table';
    case Faq = 'faq';
    case ContactForm = 'contact_form';
    case Newsletter = 'newsletter';
    case Gallery = 'gallery';
    case MapBlock = 'map';
    case SocialLinks = 'social_links';
    case Embed = 'embed';

    /**
     * @return list<string>
     */
    public static function allValues(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }

    public static function isValid(string $type): bool
    {
        return self::tryFrom($type) !== null;
    }

    /**
     * Layout / structural nodes that may contain children.
     */
    public function allowsChildren(): bool
    {
        return match ($this) {
            self::Root, self::Section, self::Columns, self::Column => true,
            default => false,
        };
    }
}

