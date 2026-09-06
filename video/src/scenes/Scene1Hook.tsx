import React from 'react';
import {
  AbsoluteFill,
  Easing,
  Img,
  interpolate,
  staticFile,
  useCurrentFrame,
} from 'remotion';
import { Audio } from '@remotion/media';
import { PrecisionGrid } from '../components/PrecisionGrid';

export const Scene1Hook: React.FC = () => {
  const frame = useCurrentFrame();

  // Slow, cinematic camera push
  const imageScale = interpolate(frame, [0, 360], [1.02, 1.14], {
    extrapolateRight: 'clamp',
  });

  // Fade in
  const containerOpacity = interpolate(frame, [0, 20], [0, 1], {
    extrapolateRight: 'clamp',
  });

  // Text animations
  const text1Opacity = interpolate(frame, [15, 35], [0, 1], {
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });
  const text1TranslateY = interpolate(frame, [15, 35], [30, 0], {
    easing: Easing.bezier(0.16, 1, 0.3, 1),
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });

  const alertOpacity = interpolate(frame, [80, 100], [0, 1], {
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });
  const alertScale = interpolate(frame, [80, 100], [0.85, 1], {
    easing: Easing.bezier(0.16, 1, 0.3, 1),
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });

  const secondaryTextOpacity = interpolate(frame, [140, 165], [0, 1], {
    extrapolateLeft: 'clamp',
    extrapolateRight: 'clamp',
  });

  return (
    <AbsoluteFill style={{ backgroundColor: '#06090F', opacity: containerOpacity }}>
      {/* Voiceover Track */}
      <Audio src={staticFile('audio/scene1.mp3')} volume={1} />

      {/* Background Mediterranean Imagery with Film Grade & Vignette */}
      <div
        style={{
          position: 'absolute',
          inset: 0,
          overflow: 'hidden',
        }}
      >
        <Img
          src={staticFile('images/marbella.jpg')}
          style={{
            width: '100%',
            height: '100%',
            objectFit: 'cover',
            transform: `scale(${imageScale})`,
            filter: 'brightness(0.38) contrast(1.15) saturate(0.85)',
          }}
        />
        {/* Obsidian Vignette Gradient */}
        <div
          style={{
            position: 'absolute',
            inset: 0,
            background:
              'radial-gradient(circle at center, rgba(6, 9, 15, 0.4) 0%, rgba(6, 9, 15, 0.95) 100%)',
          }}
        />
      </div>

      {/* HUD Telemetry Grid */}
      <PrecisionGrid
        locationLabel="MARBELLA · COSTA DEL SOL"
        coordinates="36.5101° N, 4.8824° W"
        statusLabel="SURVEILLANCE MODE"
      />

      {/* Center Content */}
      <div
        style={{
          position: 'absolute',
          inset: 0,
          display: 'flex',
          flexDirection: 'column',
          alignItems: 'center',
          justifyContent: 'center',
          textAlign: 'center',
          padding: '0 80px',
          zIndex: 10,
        }}
      >
        {/* Kicker */}
        <div
          style={{
            opacity: text1Opacity,
            transform: `translateY(${text1TranslateY}px)`,
            display: 'flex',
            alignItems: 'center',
            gap: 12,
            marginBottom: 20,
          }}
        >
          <div style={{ width: 40, height: 1, backgroundColor: '#BFA15F' }} />
          <span
            style={{
              fontSize: 14,
              letterSpacing: '0.24em',
              fontWeight: 700,
              color: '#BFA15F',
              textTransform: 'uppercase',
            }}
          >
            THE MEDITERRANEAN PROPERTY DILEMMA
          </span>
          <div style={{ width: 40, height: 1, backgroundColor: '#BFA15F' }} />
        </div>

        {/* Main Hook Headline */}
        <h1
          style={{
            opacity: text1Opacity,
            transform: `translateY(${text1TranslateY}px)`,
            fontSize: 68,
            fontWeight: 800,
            color: '#F2F5FA',
            lineHeight: 1.1,
            letterSpacing: '-0.035em',
            maxWidth: 1100,
            margin: '0 0 24px 0',
            textShadow: '0 10px 40px rgba(0,0,0,0.8)',
          }}
        >
          Buying property on the Spanish Mediterranean is one of your biggest decisions.
        </h1>

        {/* The Blind Spot Callout Card */}
        <div
          style={{
            opacity: alertOpacity,
            transform: `scale(${alertScale})`,
            display: 'inline-flex',
            alignItems: 'center',
            gap: 16,
            padding: '14px 28px',
            borderRadius: 6,
            backgroundColor: 'rgba(239, 68, 68, 0.12)',
            border: '1px solid rgba(239, 68, 68, 0.45)',
            boxShadow: '0 0 35px rgba(239, 68, 68, 0.2)',
            marginBottom: 28,
          }}
        >
          <div
            style={{
              width: 10,
              height: 10,
              borderRadius: '50%',
              backgroundColor: '#EF4444',
              boxShadow: '0 0 12px #EF4444',
            }}
          />
          <span
            style={{
              fontSize: 15,
              fontWeight: 700,
              letterSpacing: '0.14em',
              color: '#FCA5A5',
              textTransform: 'uppercase',
            }}
          >
            REAL ESTATE AGENCIES ARE PAID TO SELL · NOT TO REVEAL HIDDEN RISKS
          </span>
        </div>

        {/* Subtitle / Question */}
        <p
          style={{
            opacity: secondaryTextOpacity,
            fontSize: 24,
            fontWeight: 400,
            color: '#A9B4C6',
            lineHeight: 1.5,
            maxWidth: 820,
            margin: 0,
          }}
        >
          Who audits the neighborhood safety, noise levels, zoning revisions, and demographic trajectory{' '}
          <span style={{ color: '#EFE3C6', fontWeight: 600 }}>before</span> you sign?
        </p>
      </div>
    </AbsoluteFill>
  );
};
