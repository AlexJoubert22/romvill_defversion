import React from 'react';
import { Easing, interpolate, useCurrentFrame } from 'remotion';

interface DimensionCardProps {
  number: string;
  title: string;
  tagline: string;
  badge: string;
  sources: string;
  isActive: boolean;
  delay?: number;
}

export const DimensionCard: React.FC<DimensionCardProps> = ({
  number,
  title,
  tagline,
  badge,
  sources,
  isActive,
  delay = 0,
}) => {
  const frame = useCurrentFrame();

  const animProgress = interpolate(frame - delay, [0, 20], [0, 1], {
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
    easing: Easing.bezier(0.16, 1, 0.3, 1),
  });

  const activeGlow = isActive
    ? '0 0 28px rgba(191, 161, 95, 0.3), inset 0 0 15px rgba(191, 161, 95, 0.15)'
    : '0 4px 15px rgba(0, 0, 0, 0.3)';

  const activeBorder = isActive
    ? '1px solid rgba(191, 161, 95, 0.8)'
    : '1px solid rgba(255, 255, 255, 0.1)';

  const activeBg = isActive
    ? 'rgba(19, 26, 40, 0.9)'
    : 'rgba(13, 19, 30, 0.65)';

  return (
    <div
      style={{
        opacity: animProgress,
        transform: `translateY(${interpolate(animProgress, [0, 1], [25, 0])}px) scale(${isActive ? 1.03 : 1})`,
        padding: '20px 24px',
        borderRadius: 8,
        backgroundColor: activeBg,
        border: activeBorder,
        boxShadow: activeGlow,
        backdropFilter: 'blur(10px)',
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'space-between',
        height: 220,
        transition: 'all 0.3s ease',
      }}
    >
      <div>
        <div
          style={{
            display: 'flex',
            justifyContent: 'space-between',
            alignItems: 'center',
            marginBottom: 12,
          }}
        >
          <span
            style={{
              fontSize: 14,
              fontWeight: 800,
              color: '#BFA15F',
              letterSpacing: '0.15em',
            }}
          >
            DIMENSION {number}
          </span>
          <span
            style={{
              fontSize: 11,
              fontWeight: 700,
              padding: '3px 8px',
              borderRadius: 4,
              backgroundColor: isActive
                ? 'rgba(191, 161, 95, 0.2)'
                : 'rgba(255, 255, 255, 0.08)',
              color: isActive ? '#EFE3C6' : '#A9B4C6',
              letterSpacing: '0.1em',
              textTransform: 'uppercase',
            }}
          >
            {badge}
          </span>
        </div>

        <div
          style={{
            fontSize: 22,
            fontWeight: 700,
            color: '#F2F5FA',
            lineHeight: 1.25,
            marginBottom: 8,
          }}
        >
          {title}
        </div>

        <div
          style={{
            fontSize: 13,
            color: '#A9B4C6',
            lineHeight: 1.45,
          }}
        >
          {tagline}
        </div>
      </div>

      <div
        style={{
          borderTop: '1px solid rgba(255, 255, 255, 0.08)',
          paddingTop: 10,
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          fontSize: 11,
          color: '#6C7891',
          letterSpacing: '0.08em',
        }}
      >
        <span>VERIFICATION:</span>
        <span style={{ color: '#BFA15F', fontWeight: 600 }}>{sources}</span>
      </div>
    </div>
  );
};
