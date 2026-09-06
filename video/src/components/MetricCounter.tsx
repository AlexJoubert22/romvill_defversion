import React from 'react';
import { Easing, interpolate, useCurrentFrame } from 'remotion';

interface MetricCounterProps {
  targetNumber: number;
  suffix?: string;
  prefix?: string;
  title: string;
  subtitle: string;
  delay?: number;
}

export const MetricCounter: React.FC<MetricCounterProps> = ({
  targetNumber,
  suffix = '',
  prefix = '',
  title,
  subtitle,
  delay = 0,
}) => {
  const frame = useCurrentFrame();

  const progress = interpolate(frame - delay, [0, 45], [0, 1], {
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
    easing: Easing.bezier(0.16, 1, 0.3, 1),
  });

  const currentVal = Math.floor(progress * targetNumber);

  const opacity = interpolate(frame - delay, [0, 20], [0, 1], {
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });

  const translateY = interpolate(frame - delay, [0, 25], [30, 0], {
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
    easing: Easing.bezier(0.16, 1, 0.3, 1),
  });

  return (
    <div
      style={{
        opacity,
        transform: `translateY(${translateY}px)`,
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
        textAlign: 'center',
        padding: '24px 32px',
        backgroundColor: 'rgba(16, 22, 34, 0.65)',
        border: '1px solid rgba(191, 161, 95, 0.25)',
        borderRadius: 8,
        backdropFilter: 'blur(12px)',
        boxShadow: '0 10px 30px rgba(0, 0, 0, 0.45)',
        minWidth: 260,
      }}
    >
      <div
        style={{
          fontSize: 64,
          fontWeight: 800,
          color: '#BFA15F',
          lineHeight: 1,
          marginBottom: 10,
          fontVariantNumeric: 'tabular-nums',
          textShadow: '0 0 25px rgba(191, 161, 95, 0.4)',
          letterSpacing: '-0.03em',
        }}
      >
        {prefix}
        {currentVal}
        {suffix}
      </div>
      <div
        style={{
          fontSize: 15,
          fontWeight: 700,
          color: '#F2F5FA',
          letterSpacing: '0.12em',
          textTransform: 'uppercase',
          marginBottom: 6,
        }}
      >
        {title}
      </div>
      <div
        style={{
          fontSize: 13,
          color: '#A9B4C6',
          lineHeight: 1.4,
          maxWidth: 220,
        }}
      >
        {subtitle}
      </div>
    </div>
  );
};
